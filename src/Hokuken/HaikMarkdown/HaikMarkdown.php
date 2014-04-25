<?php
namespace Hokuken\HaikMarkdown;

use Michelf\MarkdownExtra;
use Hokuken\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface;
use Hokuken\HaikMarkdown\Plugin\Repositories\PluginRepository;
use Hokuken\HaikMarkdown\Plugin\SpecialAttributeInterface;

class HaikMarkdown extends MarkdownExtra {

    const VERSION = '0.5.1';

    /** @var boolean is parser running for prevent recursively parse */
    protected $running;

    /** @var array of PluginRepositoryInterface */
    protected $pluginRepositories = array();

    protected $hardWrap;

    /** @var array internal hashes of plugin definition */
    protected $plugins = array();

    public function __construct()
    {
        $this->running = false;

        $this->empty_element_suffix = '>';

        $this->hardWrap = false;

        $this->document_gamut = array_merge($this->document_gamut, array(
            'doConvertPlugins' => 27,
        ));
        $this->block_gamut = array_merge($this->block_gamut, array(
            'doConvertPlugins' => 10,
        ));

        $this->span_gamut = array_merge($this->span_gamut, array(
            'doInlinePlugins' => 2,
        ));

		parent::__construct();
    }

    /**
     * Set hard-wrap mode
     *
     * @param boolean hard-wrap mode on/off
     * @return $this for method chain
     */
    public function setHardWrap($hard_wrap)
    {
        $this->hardWrap = !! $hard_wrap;
        return $this;
    }

    public function transform($text)
    {
        if ($this->running)
        {
            return parent::runBlockGamut($text);
        }

        $this->running = true;

        $text = parent::transform($text);

        $this->running = false;

        return $text;
    }

    /**
     * @see Michelf\Markdown::doHardBreaks
     */
	protected function doHardBreaks($text)
	{
		# Do hard breaks:
		# when hardWrap is true then replace all break lines
		$regex = '/ {2,}\n/';
		if ($this->hardWrap)
		{
    		$regex = '/ *\n/';
		}
		return preg_replace_callback($regex, 
			array(&$this, '_doHardBreaks_callback'), $text);
	}

    /**
     * Strip plugin definition from text,
     * stores the plugin-ID and params in hash references.
     */
	protected function stripLinkDefinitions($text) {
	    $text = parent::stripLinkDefinitions($text);

		$less_than_tab = $this->tab_width - 1;

		# Link defs are in the form: ^[id]: plugin-name params, params, params ...
		# must have one more params
		$text = preg_replace_callback('{
							^[ ]{0,'.$less_than_tab.'}\[(.+)\][ ]?:	# id = $1
							  [ ]*
							  \n?				# maybe *one* newline
							  [ ]*
							(?:
							  (\S+?)			# plugin-name = $2
							)
							  [ ]*
							  \n?				# maybe one newline
							  [ ]*
							(?:
								(?<=\s)			# lookbehind for whitespace
								(.*?)			# params = $3
								[ ]*
							)	# params is required
							(?:\n+|\Z)
			}xm',
			array(&$this, '_stripPluginDefinitions_callback'),
			$text);
		return $text;
	}

    protected function _stripPluginDefinitions_callback($matches)
    {
        $ref_id = $matches[1];
        $plugin = array(
            'id' => $matches[2],
            'params' => $matches[3]
        );
        $this->plugins[$ref_id] = $plugin;
        return '';
    }

    /**
     *
     */
    protected function parseSpecialAttribute($special_attr)
    {
        if (empty($special_attr)) return [];

        # Split on components
        preg_match_all('/[#.][-_:a-zA-Z0-9]+/', $special_attr, $matches);
        $elements = $matches[0];

        # handle classes and ids (only first id taken into account)
        $classes = array();
        $id = false;
        foreach ($elements as $element) {
            if ($element{0} == '.') {
                $classes[] = substr($element, 1);
            } else if ($element{0} == '#') {
                if ($id === false) $id = substr($element, 1);
            }
        }

        return [
            'id' => $id,
            'class' => join(" ", $classes)
        ];
    }

    protected function doInlinePlugins($text)
    {
        // first, handle reference-style inline plugin
        $text = preg_replace_callback('{
            /
            \[
                ('.$this->nested_brackets_re.')	# $1: body
            \]

            [ ]?				# one optional space
            (?:\n[ ]*)?		    # one optional newline followed by spaces

            \[
                (.*?)		# $2: id
            \]
            }xs',
			array(&$this, '_doInlinePlugin_reference_callback'), $text);

        $text = preg_replace_callback('/
                \/
                (?:
                    \[
                        (?P<body>'.$this->nested_brackets_re.')  # $body
                    \]
                )?
                [ ]?
                (?:
                    \(
                        (?P<id>[a-zA-Z]\w+)   # $2: plugin name
                        (?:
                            [ ]+
                            (?P<params>[^)]*) # $3: parameter
                        )?
                    \)
                    
                )
                (?:[ ]? '.$this->id_class_attr_catch_re.' )?	 # $4 = id or class attributes
                /xs', array(&$this, '_doInlinePlugin_normal_callback'), $text);

        //last, handle reference-style shortcut: /[]
		$text = preg_replace_callback('{
		    /
            \[
                ([^\[\]]+)		# $1: id; can\'t contain [ or ]
            \]
            }xs',
            array(&$this, '_doInlinePlugin_reference_callback'), $text);

        return $text;
    }

    protected function _doInlinePlugin_reference_callback($matches)
    {
        $whole_match = $matches[0];
        $body = $matches[1];
        $ref_id = isset($matches[2]) ? $matches[2] : '';
        
        if ($ref_id === '')
        {
            $ref_id = $body;
        }

        # lower-case and turn embedded newlines into spaces
        $ref_id = strtolower($ref_id);
        $ref_id = preg_replace('{[ ]?\n}', ' ', $ref_id);

        if (isset($this->plugins[$ref_id])) {
            $plugin = $this->plugins[$ref_id];
            return $this->_doInlinePlugins($plugin['id'], $plugin['params'], $body, '', $whole_match);
        }

        return $this->hashPart($whole_match);
    }

    protected function _doInlinePlugin_normal_callback($matches)
    {
        $whole_match = $matches[0];
        $plugin_id = $matches['id'];
        $params_str = isset($matches['params']) && $matches['params'] ? $matches['params'] : '';
        $body = isset($matches['body']) ? $matches['body'] : '';
        $special_attr = isset($matches[4]) ? $matches[4] : '';

        return $this->_doInlinePlugins($plugin_id, $params_str, $body, $special_attr, $whole_match);
    }

    protected function _doInlinePlugins($plugin_id, $params = '', $body = '', $special_attr = '', $whole_match = '')
    {
        $body = $this->unhash($this->runSpanGamut($body));

        $yaml = YamlParams::adjustAsFlow($params);
        $params = YamlParams::parse($yaml);

        try {
            $plugin = $this->loadPlugin($plugin_id);
            if ($plugin instanceof SpecialAttributeInterface && $special_attr !== '')
            {
                $attrs = $this->parseSpecialAttribute($special_attr);
                foreach ($attrs as $attr => $value)
                {
                    if (empty($value)) continue;
                    $method = 'setSpecial' . ucfirst($attr) . 'Attribute';
                    $plugin->$method($value);
                }
            }
            $result = $plugin->inline($params, $body);
            return $this->hashPart($result);
        }
        catch (\RuntimeException $e) {}
        catch (\InvalidArgumentException $e) {}

        return $whole_match;
    }
    
    protected function doConvertPlugins($text)
    {
        // single line
		$text = preg_replace_callback('/
				^
				(?:
				    (:{3,})        # $1: open colons
				    [ ]*
				    (?P<id>[a-zA-Z]\w+)  # id: plugin id
				    [ ]*
				    (?P<params>.*)   # params
				    \b
				    (?:[ ]*)
				    \1             # close colons
                    (?:[ ]+ '.$this->id_class_attr_catch_re.' )?	 # $4 = id or class attributes
				)
				[ ]* (?= \n ) # Whitespace and newline following marker.
			/xm',
			array(&$this, '_doConvertPlugin_singleline_callback'), $text);
       
        // multi line
		$text = preg_replace_callback('/
				(?:\n|\A)
				# $1: Opening marker
				(
					:{3,} # 3 or more colons.
				)
				[ ]*
			    (?P<id>[a-zA-Z]\w+)  # id: plugin id
                (?:[ ]+ '.$this->id_class_attr_catch_re.' )?	 # $4 = id or class attributes
				[ ]* \n # Whitespace and newline following marker.

				# body: Content and Params
				(?P<body>
					(?>
						(?!\1 [ ]* \n)	# Not a closing marker.
						.*\n+
					)+
				)

				# Closing marker.
				\1 [ ]* (?= \n|\z )
			/xm',
			array(&$this, '_doConvertPlugin_multiline_callback'), $text);

		return $text;
    }
    
    protected function _doConvertPlugin_singleline_callback($matches)
    {
        $special_attr = isset($matches[4]) ? $matches[4] : '';
        $params_str = YamlParams::adjustAsFlow($matches['params']);
        return $this->_doConvertPlugin(
            $matches['id'],
            $params_str,
            '',
            $special_attr,
            $matches[0]
        );
    }
    
    protected function _doConvertPlugin_multiline_callback($matches)
    {
        $special_attr = isset($matches[3]) ? $matches[3] : '';
        $body = $params = '';
        if (isset($matches['body']) && trim($matches['body']))
        {
            list($params, $body) = $this->_doConvertPlugin_splitBody($matches['body']);
        }

        return $this->_doConvertPlugin(
            $matches['id'],
            $params,
            $body,
            $special_attr,
            $matches[0]
        );
    }

    protected function _doConvertPlugin_splitBody($body)
    {
        $lines = explode("\n", trim($body));
        $body_lines = $params_lines = array();
        $has_params = false;

        $lines = array_reverse($lines);
        foreach ($lines as $line)
        {
            if ( ! $has_params)
            {
                if (preg_match('/\A *-{3,} *\z/', $line))
                {
                    $has_params = true;
                    continue;
                }
                // hit internal convert plugin ending and break
                else if ( ! $has_params && preg_match('/\A:{3,} *\z/', $line))
                {
                    break;
                }
            }

            if ($has_params)
            {
                array_unshift($body_lines, $line);
            }
            else
            {
                array_unshift($params_lines, $line);
            }
        }

        if ( ! $has_params)
        {
            $params_lines = array();
            $body_lines = array_reverse($lines);
        }

        $params = join("\n", $params_lines);
        $body = join("\n", $body_lines) . "\n";

        return array($params, $body);
    }
    
    protected function _doConvertPlugin($plugin_id, $params = '', $body = '', $special_attr = '', $whole_match = '')
    {
        if ($params !== '')
        {
            $params = YamlParams::parse($params);
        }
        else
        {
            $params = array();
        }
        $body = $this->unHash($body);

        try {
            $plugin = $this->loadPlugin($plugin_id);
            if ($plugin instanceof SpecialAttributeInterface && $special_attr !== '')
            {
                $attrs = $this->parseSpecialAttribute($special_attr);
                foreach ($attrs as $attr => $value)
                {
                    if (empty($value)) continue;
                    $method = 'setSpecial' . ucfirst($attr) . 'Attribute';
                    $plugin->$method($value);
                }
            }
            $result = $plugin->convert($params, $body);
            return "\n\n".$this->hashBlock($result)."\n\n";
        }
        catch (\RuntimeException $e) {}
        catch (\InvalidArgumentException $e) {}

        return $whole_match;
    }

    /**
     * Register PluginRepository by LIFO
     *
     * @param PluginRepositoryInterface $repository
     * @return $this for method chain
     */
    public function registerPluginRepository(PluginRepositoryInterface $repository)
    {
        array_unshift($this->pluginRepositories, $repository);
        return $this;
    }

    /**
     * Load plugin instance
     *
     * @param string plugin id
     * @return PluginInterface
     * @throws \InvalidArgumentException
     */
    public function loadPlugin($plugin_id)
    {
        foreach ($this->pluginRepositories as $repository)
        {
            if ($repository->exists($plugin_id))
            {
                return $repository->load($plugin_id);
            }
        }

        throw new \InvalidArgumentException("A plugin with id=$plugin_id was not exist");
    }

    public function hasPlugin($plugin_id)
    {
        foreach ($this->pluginRepositories as $repository)
        {
            if ($repository->exists($plugin_id)) return true;
        }
        return false;
    }

    public function getAllPlugin()
    {
        $plugins = array();
        foreach ($this->pluginRepositories as $repository)
        {
            $plugins = array_merge($plugins, $repository->getAll());
        }

        $plugins = array_unique($plugins);
        sort($plugins, SORT_NATURAL | SORT_FLAG_CASE);

        return $plugins;
    }

}
