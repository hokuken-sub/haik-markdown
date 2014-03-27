<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap;

use ArrayAccess;
use IteratorAggregate;
use ArrayIterator;
use Countable;

class Row implements ArrayAccess, IteratorAggregate, Countable {

    public static $COLUMN_SIZE     = 12;

    public static $CLASS_ATTRIBUTE = 'row';

    protected $classAttribute = '';
    protected $styleAttribute = '';

    protected $columns;

    /**
     * Constructor
     *
     * @param array $columns array of Column or parsable column string
     */
    public function __construct($columns = array())
    {
        foreach ($columns as $i => $column)
        {
            if ($column instanceof Column)
            {
                //
            }
            else if (is_string($column) && Column::isParsable($column))
            {
                $columns[$i] = new Column($column);
            }
            else
            {
                unset($columns[$i]);
            }
        }
        $this->columns = array_values($columns);

        $this->addClassAttribute(self::$CLASS_ATTRIBUTE);
    }

    public function addClassAttribute($class_attr = '')
    {
        $this->classAttribute = trim($this->classAttribute . ' ' . trim($class_attr));
        return $this;
    }

    public function prependClassAttribute($class_attr = '')
    {
        $this->classAttribute = trim(trim($class_attr) . ' ' . $this->classAttribute);
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

    public function getColumn($offset)
    {
        if ( ! isset($this->columns[$offset])) return null;
        return $this->columns[$offset];
    }

    public function addColumn(Column $column)
    {
        $this->columns[] = $column;
    }

    public function setColumn($offset, Column $column)
    {
        $this->columns[$offset] = $column;
    }

    public function deleteColumn($offset)
    {
        array_splice($this->columns[$offset], $offset, 1);
    }

    public function offsetExists($offset)
    {
        return isset($this->columns[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->getColumn($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->addColumn($value);
        }
        else
        {
            $this->setColumn($offset, $value);
        }
    }

    public function offsetUnset($offset)
    {
        $this->deleteColumn($offset);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->columns);
    }

    public function count()
    {
        return count($this->columns);
    }

    public function createClassAttribute()
    {
        return $this->classAttribute;
    }

    public function createStyleAttribute()
    {
        return $this->styleAttribute;
    }

    public function renderColumns()
    {
        $columns = array();
        foreach ($this as $column)
        {
            $columns[] = $column->render();
        }
        return join("\n", $columns);
    }

    /**
     * Make html of row
     *
     * @return string html of row
     */
    public function render()
    {
        $class_attr = $this->createClassAttribute();
        $style_attr = $this->createStyleAttribute();
        $style_attr = $style_attr ? ' style="' . e($style_attr) . '"' : '';

        $columns_html = $this->renderColumns();
        return '<div class="' . e($class_attr) . '"'.$style_attr.'>' . $columns_html . '</div>';
    }

}
