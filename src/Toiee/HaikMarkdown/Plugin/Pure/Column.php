<?php
namespace Toiee\HaikMarkdown\Plugin\Pure;

use Toiee\HaikMarkdown\GridSystem\ColumnInterface;

class Column implements ColumnInterface {

    public static $PARSABLE_REGEX           = '{ ^(\d+)(?:\+(\d+))?((?:\.[a-zA-Z0-9_-]+)+)?$ }mx';
    public static $COLUMN_CLASS_PREFIX      = 'pure-u-';
    public static $DEFAULT_UNIT_NUMERATOR   = 1;
    public static $DEFAULT_UNIT_DENOMINATOR = 1;

    protected $unitNumerator;

    protected $unitDenominator;

    protected $classAttribute = '';

    protected $styleAttribute = '';

    protected $content = '';

    public function __construct($text = '')
    {
        $this->unitNumerator = self::$DEFAULT_UNIT_NUMERATOR;
        $this->unitDenominator = self::$DEFAULT_UNIT_DENOMINATOR;

        $this->parseText($text);
    }

    /**
     * Parse text to unit-numerator, unit-denominator and class attribute e.g.) 1-1 2-5 3-8.class-name
     *
     * @param  string $text column string
     * @return Columns $this for method chain
     */
    public function parseText($text = '')
    {
        if (preg_match(self::$PARSABLE_REGEX, $text, $matches))
        {
            // !TODO: 
        }
        return $this;
    }

    public function setUnitWidth($unit_numerator, $unit_denominator)
    {
        //! TODO: 
        return $this;
    }

    public function addClassAttribute($class_attr = '')
    {
        $this->classAttribute = trim($this->classAttribute . ' ' . trim($class_attr));
        return $this;
    }

    public function getClassAttribute()
    {
        return $this->classAttribute;
    }

    public function addStyleAttribute($style_declarations = '')
    {
        $this->styleAttribute = trim($this->styleAttribute . ';' . trim($style_declarations, " \t\n\r\0\x0B;"), " \t\n\r\0\x0B;");
        return $this;
    }

    public function getStyleAttribute()
    {
        return $this->styleAttribute;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * makes class for column
     *
     * @return string class for columun
     */
    public function createClassAttribute()
    {
        $classes = array();
        
        $classes[] = $this->createClassAttributeFromNumeratorAndDenominator();
        $classes[] = $this->createClassAttributeFromClassAttribute();

        $classes = array_filter($classes);

        return join(" ", $classes);
    }

    public function createClassAttributeFromNumeratorAndDenominator()
    {
        // !TODO: 
        return '';
    }

    public function createClassAttributeFromClassAttribute()
    {
        return $this->classAttribute;
    }

    /**
     * Make html of column unit
     *
     * @return string html of column unit
     */
    public function render()
    {
        $class_attr = $this->createClassAttribute();
        $style_attr = $this->getStyleAttribute();
        $style_attr = $style_attr ? ' style="' . e($style_attr) . '"' : '';
        return '<div class="' . e($class_attr) . '"'.$style_attr.'>' . $this->content . '</div>';
    }

    public static function isParsable($text)
    {
        if ( ! preg_match(self::$PARSABLE_REGEX, $text, $mts)) return false;
        
        // !TODO: 
        return true;
    }
}
