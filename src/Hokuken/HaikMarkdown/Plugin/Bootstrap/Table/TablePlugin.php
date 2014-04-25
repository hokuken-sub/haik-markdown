<?php
namespace Hokuken\HaikMarkdown\Plugin\Bootstrap\Table;

use Hokuken\HaikMarkdown\Plugin\Bootstrap\Plugin;

class TablePlugin extends Plugin {

    protected static $PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-table';
    protected static $BASE_CSS_CLASS_NAME    = 'table';
    protected static $PREFIX_CSS_CLASS_NAME  = 'table-';

    /** @var string type of this table */
    protected $type;

    /** @var boolean this table is condensed */
    protected $isCondensed;

    /** @var boolean this table is applied for responsive design */
    protected $forResponsive;

    /** @var string custom css class name for this table*/
    protected $customCssClassName;

    public function __construct(\Michelf\MarkdownInterface $parser)
    {
        parent::__construct($parser);

        $this->type = 'default';
        $this->isCondensed = false;
        $this->forResponsive = false;
        $this->customCssClassName = '';
    }

    public function convert($params = array(), $body = '')
    {
        $this->params = $params;
        $this->body = $this->parser->transform($body);

        return $this->parseParams()->renderView();
    }

    /**
     * Parse params and set options
     *
     * @return $this for method chain
     */
    protected function parseParams()
    {
        if ($this->isHash($this->params))
        {
            $this->parseHashParams();
        }
        else
        {
            $this->parseArrayParams();
        }

        return $this;
    }
    
    /**
     * parse hash array params
     */
    protected function parseHashParams()
    {
        foreach ($this->params as $key => $value)
        {
            $value = trim($value);
            switch ($key)
            {
                case 'type':
                    if (in_array($value, array('striped', 'bordered', 'hover')))
                    {
                        $this->type = $value;
                    }
                    break;
                case 'condensed':
                    $this->isCondensed = true;
                    break;
                case 'responsive':
                    $this->forResponsive = true;
                    break;
                case 'class':
                    $this->customCssClassName = trim($this->customCssClassName . ' ' . $value);
            }
        }
        return $this;
    }

    /**
     * parse array params
     * @return $this for method chain
     */
    protected function parseArrayParams()
    {
        $type_set = false;
        foreach ($this->params as $param)
        {
            switch ($param)
            {
                case 'striped':
                case 'bordered':
                case 'hover':
                    if ( ! $type_set)
                    {
                        $this->type = $param;
                        $type_set = true;
                    }
                    break;
                case 'condensed':
                    $this->isCondensed = true;
                    break;
                case 'responsive':
                    $this->forResponsive = true;
                    break;
                default:
                    $this->customCssClassName = trim($this->customCssClassName . ' ' . trim($param));
            }
        }

        return $this;
    }

    protected function getTypeClassName()
    {
        if ($this->type !== 'default')
        {
            $called_class = get_called_class();
            return $called_class::$PREFIX_CSS_CLASS_NAME . $this->type;
        }
    }

    protected function getCondensedClassName()
    {
        if ($this->isCondensed) return 'table-condensed';
    }

    protected function createClassAttribute()
    {
        $called_class = get_called_class();

        $classes = array();
        $classes[] = $called_class::$PREFIX_CLASS_ATTRIBUTE;
        $classes[] = $called_class::$BASE_CSS_CLASS_NAME;
        $classes[] = $this->getTypeClassName();
        $classes[] = $this->getCondensedClassName();
        $classes[] = $this->customCssClassName;
        

        $classes = array_filter($classes);
        return join(' ', $classes);
    }

    protected function replaceRootTableClassAttribute($html, $class_attr)
    {
        $html = preg_replace_callback('/<table(?:\s+(.*?))?>/', function($matches) use ($class_attr)
        {
            $attrs = isset($matches[1]) ? $matches[1] : '';
//            var_dump($attrs);
            if (preg_match('/\bclass\s*=\s*(\'|")(.*)?\1/', $attrs, $inner_matches))
            {
                $attrs = str_replace($inner_matches[0], '', $attrs);
                $class_attr = $inner_matches[2] . ' table';
                $attrs .= 'class="'. htmlentities($class_attr, ENT_QUOTES, 'UTF-8', false) . '"';
            }
            else
            {
                $attrs .= 'class="'. htmlentities($class_attr, ENT_QUOTES, 'UTF-8', false) .'"';
            }
            return '<table '.$attrs.'>';
        }, $html, 1);
        return $html;
    }

    protected function wrapForResponsive($html)
    {
        if ($this->forResponsive)
        {
            $html = '<div class="table-responsive">' . "\n" . $html . '</div>' . "\n";
        }
        return $html;        
    }

    public function renderView($data = array())
    {
        $class_attr = $this->createClassAttribute();
        $html = $this->replaceRootTableClassAttribute($this->body, $class_attr);
        $html = $this->wrapForResponsive($html);
        return $html;
    }

}
