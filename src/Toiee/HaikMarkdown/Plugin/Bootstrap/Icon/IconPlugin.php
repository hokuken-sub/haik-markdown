<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap\Icon;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin;

class IconPlugin extends Plugin {

    protected static $PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-icon';
    protected static $BASE_CSS_CLASS_NAME    = 'glyphicon';
    protected static $PREFIX_CSS_CLASS_NAME  = 'glyphicon-';
    protected static $ICON_NAME_REGEX        = '/\A[0-9a-zA-Z_-]+\z/';

    /** @var string glyphicon icon-name */
    protected $iconName;

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
        foreach ($this->params as $param)
        {
            $icon_name = trim($param);
            if ($this->validateIconName($icon_name))
            {
                $this->iconName = $icon_name;
                break;
            }
        }
        return $this;
    }

    protected function validateIconName($icon_name)
    {
        $called_class = get_called_class();
        return preg_match($called_class::$ICON_NAME_REGEX, $icon_name);
    }

    protected function getIconClassName()
    {
        $called_class = get_called_class();
        return $called_class::$PREFIX_CSS_CLASS_NAME . $this->iconName;
    }

    protected function createClassAttribute()
    {
        $called_class = get_called_class();

        $classes = array();
        $classes[] = $called_class::$PREFIX_CLASS_ATTRIBUTE;
        $classes[] = $called_class::$BASE_CSS_CLASS_NAME;
        $classes[] = $this->getIconClassName();
        $classes = array_filter($classes);
        
        return $this->classAttribute = trim(join(' ', $classes));
    }

    public function renderView($data = array())
    {
        if ( ! $this->iconName) return '';

        $class_attr = $this->createClassAttribute();
        return '<i class="'.e($class_attr).'"></i>';
    }

}
