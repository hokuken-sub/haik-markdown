<?php
namespace Toiee\HaikMarkdown;

use Michelf\MarkdownExtra;
use Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface;
use Toiee\HaikMarkdown\Plugin\Repositories\PluginRepository;

class HaikMarkdown extends MarkdownExtra {

    /** @var boolean is parser running for prevent recursively parse */
    protected $running;

    /** @var array of PluginRepositoryInterface */
    protected $pluginRepositories = array();


    public function __construct()
    {
        $this->running = false;

        $this->empty_element_suffix = '>';
        
        $this->document_gamut += array(
            'doConvertPlugins'   => 10,
        );

		$this->span_gamut += array(
            "doInlinePlugins"    => 2,
        );

		parent::__construct();
    }

    public function transform($text)
    {
        if ($this->running)
        {
            return with(new self())->transform($text);
        }

        $this->running = true;

        $text = parent::transform($text);

        $this->running = false;

        return $text;
    }

    protected function doInlinePlugins($text)
    {
        $text = preg_replace_callback('/
                &
                (      # (1) plain
                  (\w+) # (2) plugin name
                  (?:
                    \(
                      ((?:(?!\)[;{]).)*) # (3) parameter
                    \)
                  )?
                )
                (?:
                  \{
                    ((?:(?R)|(?!};).)*) # (4) body
                  \}
                )?
                ;
			/xs', array(&$this, '_doInlinePlugins_callback'), $text);

        return $text;
    }

    protected function _doInlinePlugins_callback($matches)
    {
        $whole_match = $matches[0];
        $plugin_id = $matches[2];
        $params = isset($matches[3]) && $matches[3] ? str_getcsv($matches[3], ',', '"', '\\') : array();
        $body = isset($matches[4]) ? $this->unhash($this->runSpanGamut($matches[4])) : '';

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
				(?:\n|\A)
				(?:
				    \{\#
				        (\w+)   # (1) plugin name
				        (?:
				            \(
				            ([^\n]*) # (2) parameter
				            \)
				        )?
				    \}
				)
				[ ]* (?= \n ) # Whitespace and newline following marker.
			/xm',
			array(&$this, '_doConvertPlugin_singleline_callback'), $text);
       
        // multi line
		$text = preg_replace_callback('/
				(?:\n|\A)
				# (1) Opening marker
				(
					(?::{3,}) # 3 or more colons.
				)
				[ ]*
				(?:
				    \{\#
				        (\w+)   # (2) plugin name
				        (?:
				            \(
				            ((?:(?!\n).)*) # (3) parameter
				            \)
				        )?
				    \}
				)
				[ :]* \n # Whitespace and newline following marker.
				
				# (4) Content
				(
					(?>
						(?!\1 [ ]* \n)	# Not a closing marker.
						.*\n+
					)+
				)
				
				# Closing marker.
				\1 [ ]* (?= \n )
			/xm',
			array(&$this, '_doConvertPlugin_multiline_callback'), $text);

		return $text;
    }
    
    protected function _doConvertPlugin_singleline_callback($matches)
    {
        return $this->_doConvertPlugin(
            $matches[1],
            isset($matches[2]) ? $matches[2] : '',
            '',
            $matches[0]
        );
    }
    
    protected function _doConvertPlugin_multiline_callback($matches)
    {
        return $this->_doConvertPlugin(
            $matches[2],
            isset($matches[3]) ? $matches[3] : '',
            isset($matches[4]) ? $matches[4] : '',
            $matches[0]
        );
    }
    
    protected function _doConvertPlugin($plugin_id, $params = '', $body = '', $whole_match = '')
    {
        $params = ($params !== '') ? str_getcsv($params, ',', '"', '\\') : array();
        $body = $this->unHash($body);

        try {
            $result = with($this->loadPlugin($plugin_id))->convert($params, $body);
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
