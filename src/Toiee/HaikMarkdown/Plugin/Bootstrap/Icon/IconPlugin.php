<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap\Icon;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin;

class IconPlugin extends Plugin {

    protected static $CLASS_ATTRIBUTE_PREFIX = 'haik-plugin-icon';
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
        if (count($params) === 0)
        {
            return '';
        }

        foreach ($params as $param)
        {
            $icon_name = trim($param);
            if ($this->validateIconName($icon_name))
            {
                $this->iconName = $icon_name;
                break;
            }
        }
        return $this->renderView();
    }

    protected function validateIconName($icon_name)
    {
        return preg_match(self::$ICON_NAME_REGEX, $icon_name);
    }

    protected function getIconClassName()
    {
        return self::$PREFIX_CSS_CLASS_NAME . $this->iconName;
    }

    protected function createClassAttribute()
    {
        $classes = array();
        $classes[] = self::$CLASS_ATTRIBUTE_PREFIX;
        $classes[] = self::$BASE_CSS_CLASS_NAME;
        $classes[] = $this->getIconClassName();

        return $this->classAttribute = trim(join(' ', $classes));
    }

    public function renderView($data = array())
    {
        $class_attr = $this->createClassAttribute();
        return '<i class="'.e($class_attr).'"></i>';
    }

}
