<?php
namespace Toiee\HaikMarkdown\Plugin\Pure;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Row as BootstrapRow;

class Row extends BootstrapRow {

    const COLUMN_CLASS_NAME = '\Toiee\HaikMarkdown\Plugin\Pure\Column';

    public static $NO_RESPONSIVE_CLASS_NAME = 'pure-g';
    public static $RESPONSIVE_CLASS_NAME    = 'pure-g-r';

    protected function initialize()
    {
        $this->setResponsive();
    }

    /**
     * Set behavior of grid responsive/no-responsive.
     * If switch behavior then modify class attribute.
     *
     * @param boolean $responsive when false set to no-responsive mode
     * @return $this for method chain
     */
    public function setResponsive($responsive = true)
    {
        $class_attr = $this->getClassAttribute();
        if ($responsive)
        {
            $remove_class_name = self::$NO_RESPONSIVE_CLASS_NAME;
            $add_class_name = self::$RESPONSIVE_CLASS_NAME;
        }
        else
        {
            $remove_class_name = self::$RESPONSIVE_CLASS_NAME;
            $add_class_name = self::$NO_RESPONSIVE_CLASS_NAME;
        }
        $regex = '{ (?: [ ] | \A) '. $remove_class_name .' (?: [ ] | \z) }x';
        if (preg_match($regex, $class_attr))
        {
            $class_attr = trim(preg_replace($regex, ' ' . $add_class_name . ' ', $class_attr));
            $this->classAttribute = $class_attr;
        }
        else
        {
            $this->addClassAttribute($add_class_name);
        }

        return $this;
    }

}
