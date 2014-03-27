<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap;

use Toiee\HaikMarkdown\GridSystem\ColumnInterface;

class Column implements ColumnInterface {

    public static $MAX_WIDTH            = 12;
    public static $MAX_OFFSET_WIDTH     = 11;
    public static $DEFAULT_OFFSET_WIDTH = 0;
    public static $PARSABLE_REGEX       = '{ ^(\d+)(?:\+(\d+))?((?:\.[a-zA-Z0-9_-]+)+)?$ }mx';

    public static $ROW_CLASS_NAME       = 'row';
    public static $COLUMN_CLASS_PREFIX  = 'col-sm-';
    public static $OFFSET_CLASS_PREFIX  = 'col-sm-offset-';

    protected $attributesForClasses = array('columnWidth', 'offsetWidth', 'classAttribute');
    protected $columnWidth;

    protected $offsetWidth;

    protected $classAttribute = '';

    protected $styleAttribute = '';

    protected $content = '';

    public function __construct($text = '')
    {
        $this->columnWidth = self::$MAX_WIDTH;
        $this->offsetWidth = self::$DEFAULT_OFFSET_WIDTH;

        $this->parseText($text);
    }

    /**
     * Parse text to columns, offsetColumns and classAttribute for column (ie. 3, 3+3, 3.customclass)
     * @param  string $text column string
     * @return Columns $this for method chain
     */
    public function parseText($text = '')
    {
        if (preg_match(self::$PARSABLE_REGEX, $text, $matches))
        {
            $this->setColumnWidth(!empty($matches[1]) ? $matches[1] : self::$MAX_WIDTH);
            $this->setOffsetWidth(!empty($matches[2]) ? $matches[2] : self::$DEFAULT_OFFSET_WIDTH);
            $this->addClassAttribute(!empty($matches[3]) ? trim(str_replace('.', ' ', $matches[3])) : '');
        }
        return $this;
    }

    public function setColumnWidth($column_width)
    {
        $column_width = (int)$column_width;
        $this->columnWidth = ($column_width > self::$MAX_WIDTH OR $column_width < 1) ? self::$MAX_WIDTH : $column_width;
        return $this;
    }

    public function getColumnWidth()
    {
        return $this->columnWidth;
    }

    public function setOffsetWidth($offset_width)
    {
        $offset_width = (int)$offset_width;
        if ($offset_width > self::$MAX_OFFSET_WIDTH)
        {
            $offset_width = self::$MAX_OFFSET_WIDTH;
        }
        else if ($offset_width < 1)
        {
            $offset_width = 0;
        }
        $this->offsetWidth = $offset_width;
        return $this;
    }

    public function getOffsetWidth()
    {
        return $this->offsetWidth;
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
        
        foreach ($this->attributesForClasses as $attribute)
        {
            $method = 'createClassAttributeFrom' . ucfirst($attribute);
            $classes[] = $this->$method();
        }
        $classes = array_filter($classes);

        return join(" ", $classes);
    }

    public function createClassAttributeFromColumnWidth()
    {
        $prefix = self::$COLUMN_CLASS_PREFIX;
        return $prefix . $this->columnWidth;
    }

    public function createClassAttributeFromOffsetWidth()
    {
        $prefix = self::$OFFSET_CLASS_PREFIX;
        return $this->offsetWidth ? $prefix . $this->offsetWidth : null;
    }

    public function createClassAttributeFromClassAttribute()
    {
        return $this->classAttribute;
    }

    public function createStyleAttribute()
    {
        return $this->styleAttribute;
    }

    /**
     * Make html of column unit
     *
     * @return string html of column unit
     */
    public function render()
    {
        $class_attr = $this->createClassAttribute();
        $style_attr = $this->createStyleAttribute();
        $style_attr = $style_attr ? ' style="' . e($style_attr) . '"' : '';
        return '<div class="' . e($class_attr) . '"'.$style_attr.'>' . $this->content . '</div>';
    }

    /**
     * Make html of column unit with row
     *
     * @return string html of column unit with row
     */
    public function renderWithRow()
    {
        $column_html = $this->render();

        return '<div class="'. e(self::$ROW_CLASS_NAME) .'">'. $column_html .'</div>';
    }

    public static function isParsable($text)
    {
        return preg_match(self::$PARSABLE_REGEX, $text);
    }
}
