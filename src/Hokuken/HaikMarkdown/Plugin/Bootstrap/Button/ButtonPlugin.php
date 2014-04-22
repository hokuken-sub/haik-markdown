<?php
namespace Hokuken\HaikMarkdown\Plugin\Bootstrap\Button;

use Hokuken\HaikMarkdown\Plugin\Bootstrap\Plugin;
use Michelf\MarkdownInterface;

class ButtonPlugin extends Plugin {

    protected static $PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-button';
    protected static $BASE_CSS_CLASS_NAME    = 'btn';
    protected static $PREFIX_CSS_CLASS_NAME  = 'btn-';
    protected static $DEFAULT_URL             = '#';
    protected static $DEFAULT_TYPE            = 'default';
    protected static $DEFAULT_SIZE            = 'medium';

    protected $classAttribute;
    /** @var string URL to move when the button push */
    protected $url;

    /** @var string button type */
    protected $type;

    /** @var string button size */
    protected $size;

    /** @var boolean block type */
    protected $block;

    protected $customCssClassName;

    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);

        $this->classAttribute = '';
        $this->url = self::$DEFAULT_URL;
        $this->type = self::$DEFAULT_TYPE;
        $this->size = self::$DEFAULT_SIZE;
        $this->block = false;
        $this->customCssClassName = '';
    }

    /**
     * inline call via HaikMarkdown &plugin-name(...){...};
     * @params array $params
     * @params string $body when {...} was set
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    function inline($params = array(), $body = '')
    {
        $this->params = $params;
        $this->body = $body;
        $this->parseParams();

        return $this->renderView();
    }

    public function convert($params = array(), $body = '')
    {
        $this->params = $params;
        $this->body = $body;
        $this->parseParams();
        $this->block = true;
        $this->size = 'large';

        return $this->renderView();
    }

    public function parseParams()
    {
        if ($this->isHash($this->params))
        {
            $this->parseHashParams();
        }
        else
        {
            $this->parseArrayParams();
        }
    }

    protected function parseArrayParams()
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
                case 'info':
                case 'success':
                case 'warning':
                case 'danger':
                case 'link':
                case 'default':
                    $this->type = $param;
                    break;
                case 'large':
                case 'lg':
                    $this->size = 'large';
                    break;
                case 'small':
                case 'sm':
                    $this->size = 'small';
                    break;
                case 'mini':
                case 'xs':
                    $this->size = 'x-small';
                    break;
                case 'block':
                    $this->block = true;
                    break;
                default:
                    $this->customCssClassName = trim($this->customCssClassName . ' ' . trim($param));
            }
        }
    }

    protected function parseHashParams()
    {
        foreach($this->params as $key => $value)
        {
            $value = trim($value);
            switch ($key)
            {
                case 'url':
                case 'href':
                case 'path':
                    $this->setUrl($value);
                    break;
                case 'type':
                    if (in_array($value, ['primary', 'info', 'success', 'warning', 'danger', 'link', 'default']))
                    {
                        $this->type = $value;
                    }
                    break;
                case 'size':
                    if (in_array($value, ['large', 'lg', 'small', 'sm', 'x-small', 'mini', 'xs']))
                    {
                        $this->size = $value;
                    }
                    break;
                case 'block':
                    $this->block = true;
                    break;
                case 'class':
                    $this->customCssClassName = trim($this->customCssClassName . ' ' . trim($value));
                    break;
            }
        }        
    }

    protected function setUrl($url)
    {
        $this->url = $url;
    }

    protected function getTypeClassName()
    {
        return self::$PREFIX_CSS_CLASS_NAME . $this->type;
    }

    protected function getSizeClassName()
    {
        $class_name = self::$PREFIX_CSS_CLASS_NAME;

        switch ($this->size)
        {
            case 'large':
                $class_name .= 'lg';
                break;
            case 'small':
                $class_name .= 'sm';
                break;
            case 'x-small':
                $class_name .= 'xs';
                break;
            case 'medium':
            default:
                return '';
        }

        return $class_name;
    }

    protected function getBlockClassName()
    {
        return self::$PREFIX_CSS_CLASS_NAME . 'block';
    }
    protected function createClassAttribute()
    {
        $classes = array();
        $classes[] = self::$PREFIX_CLASS_ATTRIBUTE;
        $classes[] = self::$BASE_CSS_CLASS_NAME;
        $classes[] = $this->getTypeClassName();
        $size_class = $this->getSizeClassName();
        if ($this->block)
        {
            $classes[] = $this->getBlockClassName();
        }
        if ($size_class !== '')
        {
            $classes[] = $size_class;
        }
        $classes[] = $this->customCssClassName;
        
        return $this->classAttribute = trim(join(' ', $classes));
    }

    public function renderView($data = array())
    {
        $class_attr = $this->createClassAttribute();
        $url = $this->url;
        $html = '<a class="'. e($class_attr) .'" href="'.e($url).'">'.$this->body.'</a>';
        return $html;
    }
}
