<?php
namespace Toiee\HaikMarkdown;

use Michelf\MarkdownExtra;
use Toiee\HaikMarkdown\Plugin\PluginRepository;

class HaikMarkdown extends MarkdownExtra {

    protected $plugins;

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

        $this->plugins = PluginRepository::getInstance();
		
		parent::__construct();
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
            $result = $this->plugins->load($plugin_id)->inline($params, $body);
        }
        catch (\InvalidArgumentException $e) {
            return $whole_match;
        }
        return $this->hashPart($result);        
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
            $result = $this->plugins->load($plugin_id)->convert($params, $body);
            return "\n\n".$this->hashBlock($result)."\n\n";
        }
        catch (\InvalidArgumentException $e)
        {
            return $whole_match;
        }
    }

}
