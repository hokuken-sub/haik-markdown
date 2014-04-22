<?php
namespace Hokuken\HaikMarkdown\Plugin\Bootstrap\Label;

use Hokuken\HaikMarkdown\Plugin\Bootstrap\Plugin;

class LabelPlugin extends Plugin {

    protected static $PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-label';
    protected static $BASE_CSS_CLASS_NAME    = 'label';
    protected static $PREFIX_CSS_CLASS_NAME  = 'label-';
    protected static $DEFAULT_TYPE           = 'default';

    /** @var string button type */
    protected $type;

    protected $customCssClassName;

    public function __construct(\Michelf\MarkdownInterface $parser)
    {
        parent::__construct($parser);

        $this->type = self::$DEFAULT_TYPE;
        $this->customCssClassName = '';
    }

    /**
     * inline call via HaikMarkdown &plugin-name(...);
     * @params array $params
     * @params string $body when {...} was set
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    function inline($params = array(), $body = '')
    {
        $this->params = $params;
        $this->body = $body;
        
        return $this->parseParams()->renderView();
    }

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

    protected function parseArrayParams()
    {
        foreach ($this->params as $param)
        {
            $param = trim($param);
            switch ($param)
            {
                case 'default':
                case 'primary':
                case 'success':
                case 'info'   :
                case 'warning':
                case 'danger' :
                    $this->type = $param;    
                    break;
                default :
                    $this->customCssClassName = trim($this->customCssClassName . ' ' . trim($param));
            }
        }
    }

    protected function parseHashParams()
    {
        foreach ($this->params as $key => $value)
        {
            $value = trim($value);
            switch ($key)
            {
                case 'type':
                    if (in_array($value, ['default', 'primary', 'success', 'info', 'warning', 'danger']))
                    {
                        $this->type = $value;
                    }
                    break;
                case 'class':
                    $this->customCssClassName = trim($this->customCssClassName . ' ' . trim($value));
            }
        }
    }

    protected function getTypeClassName()
    {
        return self::$PREFIX_CSS_CLASS_NAME . $this->type;
    }

    protected function createClassAttribute()
    {
        $classes = array();
        $classes[] = self::$PREFIX_CLASS_ATTRIBUTE;
        $classes[] = self::$BASE_CSS_CLASS_NAME;
        $classes[] = $this->getTypeClassName();
        $classes[] = $this->customCssClassName;
        
        return $this->classAttribute = trim(join(' ', $classes));
    }

    public function renderView($data = array())
    {
        $class_attr = $this->createClassAttribute();
        return '<span class="'. e($class_attr) .'">'.$this->body.'</span>';
    }

}
