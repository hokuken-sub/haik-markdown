<?php
namespace Toiee\HaikMarkdown\Plugin\Pure\Button;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Button\ButtonPlugin as BootstrapButtonPlugin;
use Michelf\MarkdownInterface;

class ButtonPlugin extends BootstrapButtonPlugin {

    protected static $BASE_CSS_CLASS_NAME    = 'pure-button';
    protected static $PREFIX_CSS_CLASS_NAME  = 'pure-button-';
    protected static $DEFAULT_URL             = '#';
    protected static $DEFAULT_TYPE            = 'default';

    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);

        $this->classAttribute = '';
        $this->url = self::$DEFAULT_URL;
        $this->type = self::$DEFAULT_TYPE;
        $this->customCssClassName = '';
    }

    /**
     * Convert method and Inline method is same
     */
    public function convert($params = array(), $body = '')
    {
        return $this->inline($params, $body);
    }

    public function parseParams()
    {
        $params = $this->params;
        if (count($params) > 0)
        {
            $this->setUrl(array_shift($params));
        }

        foreach($params as $param)
        {
            switch ($param)
            {
                case 'primary':
                case 'default':
                    $this->type = $param;
                    break;
                default:
                    $this->customCssClassName = trim($this->customCssClassName . ' ' . trim($param));
            }
        }        
    }

    protected function getTypeClassName()
    {
        if ($this->type === 'default') return '';
        return self::$PREFIX_CSS_CLASS_NAME . $this->type;
    }

    protected function createClassAttribute()
    {
        $classes = array();
        $classes[] = self::$PREFIX_CLASS_ATTRIBUTE;
        $classes[] = self::$BASE_CSS_CLASS_NAME;
        $classes[] = $this->getTypeClassName();
        $classes[] = $this->customCssClassName;
        $classes = array_filter($classes);

        return $this->classAttribute = trim(join(' ', $classes));
    }

}
