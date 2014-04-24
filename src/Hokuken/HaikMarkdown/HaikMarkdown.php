<?php
namespace Hokuken\HaikMarkdown;

use Michelf\MarkdownExtra;
use Hokuken\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface;
use Hokuken\HaikMarkdown\Plugin\Repositories\PluginRepository;
use Hokuken\HaikMarkdown\Plugin\SpecialAttributeInterface;

class HaikMarkdown extends MarkdownExtra {

    const VERSION = '0.5.0';

    /** @var boolean is parser running for prevent recursively parse */
    protected $running;

    /** @var array of PluginRepositoryInterface */
    protected $pluginRepositories = array();

    protected $hardWrap;

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
        $text = preg_replace_callback('/
                \/
                (?:
                    \[
                        (?P<body>'.$this->nested_brackets_re.')  # $1: body
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
			/xs', array(&$this, '_doInlinePlugins_callback'), $text);

        return $text;
    }

    protected function _doInlinePlugins_callback($matches)
    {
        $whole_match = $matches[0];
        $plugin_id = $matches['id'];
        $params_str = isset($matches['params']) && $matches['params'] ? $matches['params'] : '';
        $body = isset($matches['body']) ? $this->unhash($this->runSpanGamut($matches['body'])) : '';

        $yaml = YamlParams::adjustAsFlow($params_str);
        $params = YamlParams::parse($yaml);

        try {
            $result = with($this->loadPlugin($plugin_id))->inline($params, $body);
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
