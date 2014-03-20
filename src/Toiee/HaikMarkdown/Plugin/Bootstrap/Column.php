<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap;

class Column {

    const MAX_WIDTH            = 12;
    const DEFAULT_OFFSET_WIDTH = 0;
    const PARSABLE_REGEX       = '{ ^(\d+)(?:\+(\d+))?((?:\.[a-zA-Z0-9_-]+)+)?$ }mx';

    protected $columnWidth;

    protected $offsetWidth;

    protected $classAttribute = '';

    protected $styleAttribute = '';

    protected $content = '';

    public function __construct($text = '')
    {
        $this->columnWidth = self::MAX_WIDTH;
        $this->offsetWidth = self::DEFAULT_OFFSET_WIDTH;

        $this->parseText($text);
    }

    /**
     * Parse text to columns, offsetColumns and classAttribute for column (ie. 3, 3+3, 3.customclass)
     * @param  string $text column string
     * @return Columns $this for method chain
     */
    public function parseText($text = '')
    {
        if (preg_match(self::PARSABLE_REGEX, $text, $matches))
        {
            $this->setColumnWidth(!empty($matches[1]) ? $matches[1] : self::MAX_WIDTH);
            $this->setOffsetWidth(!empty($matches[2]) ? $matches[2] : self::DEFAULT_OFFSET_WIDTH);
            $this->addClassAttribute(!empty($matches[3]) ? trim(str_replace('.', ' ', $matches[3])) : '');
        }
        return $this;
    }

    public function setColumnWidth($column_width)
    {
        $this->columnWidth = (int)$column_width;
        return $this;
    }

    public function getColumnWidth()
    {
        return $this->columnsWidth;
    }

    public function setOffsetColumns($offset_width)
    {
        $this->offsetWidth = (int)$offset_width;
        return $this;
    }

    public function getOffsetColumns()
    {
        return $this->offsetWidth();
    }

    public function addClassAttribute($class_attr = '')
    {
        $this->classAttribute = trim($this->classAttribute . ' ' . trim($class_attr));
        return $this;
    }

    public function getClassAttribute()
    {
        return $this->classAttribute();
    }

    public function addStyleAttribute($style_declarations = '')
    {
        $this->styleAttribute = trim($this->styleAttribute . ';' . trim($style_declarations, " \t\n\r\0\x0B;"), " \t\n\r\0\x0B;");
        return $this;
    }

    public function getStyleAttribute()
    {
        return $this->styleAttribute();
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
        
        if ( ! isset($this->data['cols']))
        {
            return '';
        }
    
        foreach($this->data as $key => $value)
        {
            $option = '';
            switch($key)
            {
                case 'offset':
                    $option = "offset-";
                case 'cols':
                    if ($value > 0)
                    {
                        $classes[] = 'col-sm-'. $option . $value;
                    }
                    break;
                case 'class':
                    if ($value !== '')
                    {
                        $classes[] = $value;
                    }
                    break;
            }
        }

        return join(" ", $classes);

        return '';
    }

    /**
     * makes wrapped html with column class
     *
     * @param  string content html
     * @return string wrapped html with column class
     */
    public function wrap()
    {
        $class_attr = $this->createClassAttribute();

        if ($class_attr === '')
        {
            return $this->content;
        }

        $html = '<div class="row"><div class="'.e($class_attr).'">'.$this->content.'</div></div>';

        return $html;
    }

    public static function isParsable($text)
    {
        return preg_match(self::PARSABLE_REGEX, $text);
    }
}
