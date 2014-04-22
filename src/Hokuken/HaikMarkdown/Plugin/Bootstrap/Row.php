<?php
namespace Hokuken\HaikMarkdown\Plugin\Bootstrap;

use ArrayAccess;
use IteratorAggregate;
use ArrayIterator;
use Countable;
use Hokuken\HaikMarkdown\GridSystem\ColumnInterface;

class Row implements ArrayAccess, IteratorAggregate, Countable {

    const COLUMN_CLASS_NAME = '\Hokuken\HaikMarkdown\Plugin\Bootstrap\Column';

    public static $COLUMN_SIZE     = 12;
    public static $CLASS_ATTRIBUTE = 'row';

    protected $classAttribute = '';
    protected $styleAttribute = '';

    protected $columns;

    /**
     * Constructor
     *
     * @param array $columns array of ColumnInterface or parsable column string
     */
    public function __construct($columns = array())
    {
        $called_class_name = get_called_class();
        $column_class_name = $called_class_name::COLUMN_CLASS_NAME;

        foreach ($columns as $i => $column)
        {
            if ($column instanceof ColumnInterface)
            {
                //
            }
            else if (is_string($column) && $column_class_name::isParsable($column))
            {
                $columns[$i] = new $column_class_name($column);
            }
            else
            {
                unset($columns[$i]);
            }
        }
        $this->columns = array_values($columns);

        $this->initialize();
    }

    protected function initialize()
    {
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

    public function addColumn(ColumnInterface $column)
    {
        $this->columns[] = $column;
    }

    public function setColumn($offset, ColumnInterface $column)
    {
        $this->columns[$offset] = $column;
    }

    public function deleteColumn($offset)
    {
        array_splice($this->columns, $offset, 1);
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
        return $this->getStyleAttribute();
    }

    protected function renderColumns()
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
