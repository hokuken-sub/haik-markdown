<?php
namespace Hokuken\HaikMarkdown\Plugin\Bootstrap\Cols;

use Hokuken\HaikMarkdown\HaikMarkdown;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Plugin;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Row;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Column;
use Michelf\MarkdownInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class ColsPlugin extends Plugin {

    const COL_DELIMITER   = "\n====\n";

    public static $PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-cols';

    protected $delimiter;

    protected $row;
    protected $colBase;
    
    protected $params;
    protected $body;
    
    protected $violateColumnSize;

    protected $view = 'cols.template';
    
    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);

        $class_name = get_called_class();
        $this->row = $this->createRow()->prependClassAttribute($class_name::$PREFIX_CLASS_ATTRIBUTE);
        $this->setDelimiter(self::COL_DELIMITER);
        $this->violateColumnSize = false;
    }

    /**
     * Create Row instance
     *
     * @return Row
     */
    protected function createRow()
    {
        return new Row();
    }

    /**
     * Create Column instance
     *
     * @param 
     * @return Hokuken\HaikMarkdown\GridSystem\ColumnInterface
     */
    protected function createColumn($text = '')
    {
        return new Column($text);
    }

    /**
     * convert call via HaikMarkdown :::{plugin-name(...):::
     * @params array $params
     * @params string $body when {...} was set
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    public function convert($params = array(), $body = '')
    {

        // set params
        $this->params = $params;
        $this->body = $body;
        
        $this->parseParams();
        $this->parseBody();
        
        $this->validatesColumnSize();

        $this->parseColumns();

        $html = $this->renderView();

        return $html;
    }

    protected function validatesColumnSize()
    {
        $row_class_name = get_class($this->row);
        if ($this->getTotalColumnSize() > $row_class_name::$COLUMN_SIZE)
        {
            $this->violateColumnSize = true;
        }
    }

    protected function getTotalColumnSize()
    {
        $total_columns = 0;
        foreach ($this->row as $column)
        {
            $total_columns += $column->getColumnWidth() + $column->getOffsetWidth();
        }
        return $total_columns;
    }

    /**
     * parse params
     */
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
    }

    protected function parseArrayParams()
    {
        foreach ($this->params as $param)
        {
            if (is_array($param))
            {
                foreach ($param as $key => $value)
                {
                    switch ($key)
                    {
                        case 'class':
                            $this->row->addClassAttribute($value);
                            break;
                        case 'delimiter':
                        case 'delim':
                        case 'separator':
                        case 'sep':
                            $this->setDelimiter($value);
                    }
                }
                continue;
            }
            if ($this->columnIsParsable($param))
            {
                $this->addColumns($param);
            }
            else
            {
                // if you want change delimiter
                $this->setDelimiter($param);
            }
        }
    }

    protected function parseHashParams()
    {
        foreach ($this->params as $key => $value)
        {
            switch ($key)
            {
                case 'class':
                    $this->row->addClassAttribute($value);
                    break;
                case 'style':
                    $this->row->addStyleAttribute($value);
                    break;
                case 'delimiter':
                case 'delim':
                case 'separator':
                case 'sep':
                    $this->setDelimiter($value);
                    break;
                case 'cols':
                case 'columns':
                    if (is_array($value) && ! $this->isHash($value))
                    {
                        foreach ($value as $column)
                        {
                            if (is_string($column) && $this->columnIsParsable($column))
                            {
                                $this->addColumns($column);
                            }
                            else if (is_array($column) && isset($column['span']) && $this->columnIsParsable($column['span']))
                            {
                                $column_obj = $this->createColumn($column['span']);
                                if (isset($column['offset'])) $column_obj->setOffsetWidth($column['offset']);
                                if (isset($column['class'])) $column_obj->addClassAttribute($column['class']);
                                if (isset($column['style'])) $column_obj->addStyleAttribute($column['style']);
                                $this->row[] = $column_obj;
                            }
                        }
                    }
                    else if ($this->columnIsParsable($value))
                    {
                        $this->addColumns($value);
                    }
                    else
                    {
                        try {
                            $columns = Yaml::parse('[' . $value . ']');
                            foreach ($columns as $column)
                            {
                                if ($this->columnIsParsable($column))
                                {
                                    $this->addColumns($column);
                                }
                            }
                        }
                        catch (ParseException $e) {}
                    }
                
            }
        }        
    }

    protected function setDelimiter($delimiter)
    {
        $delimiter = trim((string)$delimiter);
        if ($delimiter === '') return;
        $this->delimiter = "\n" . $delimiter . "\n";
    }

    protected function columnIsParsable($text)
    {
        return Column::isParsable($text);
    }

    /**
     * Add columns by text
     *
     * @param string|ColumnInterface $text Column::isParsable is true or Column object
     * @return void
     */
    protected function addColumns($text)
    {
        $column = $this->createColumn($text);
        $this->row[] = $column;
    }

    /**
     * Set columns by body
     *
     * @return void
     */
    protected function setColumnsByBody()
    {
        if (count($this->row) === 0)
        {
            // if parameter is not set then make cols with body
        	$data = explode($this->delimiter, $this->body);
        	$row_class_name = get_class($this->row);
    		$col_width = (int)($row_class_name::$COLUMN_SIZE / count($data));
    		for ($i = 0; $i < count($data); $i++)
    		{
    		    $column = $this->createColumn()->setColumnWidth($col_width);
                $this->row[$i] = $column;
    		}
        }
    }
    /**
     * parse body
     */
    protected function parseBody()
    {
        $this->setColumnsByBody();

        // if parameter and body delimiter is not match then bind body over cols
        $col_num = count($this->row);
        $data = array_pad(explode($this->delimiter, $this->body, $col_num), $col_num, '');

    	for ($i = 0; $i < $col_num; $i++)
    	{
    	    $column = $this->row[$i];
    		if (isset($data[$i]))
    		{
                $data[$i] = preg_replace_callback('{ (?:\A|\n)STYLE:(.+)(?:\z|\n) }xm', function($matches) use ($column)
    		    {
                    $style_attribute = $matches[1];
                    $column->addStyleAttribute($style_attribute);
                    return "\n";
                }, $data[$i]);

                $data[$i] = preg_replace_callback('{ (?:\A|\n)CLASS:(.+)(?:\z|\n) }xm', function($matches) use ($column)
    		    {
                    $class_attribute = $matches[1];
                    $column->addClassAttribute($class_attribute);
                    return "\n";
                }, $data[$i]);

                $column->setContent($data[$i]);
    		}
    	}
    }

    /**
     * Parse columns content's markdown
     */
    protected function parseColumns()
    {
        foreach ($this->row as $i => $column)
        {
            $this->row[$i]->setContent(trim($this->parser->transform($column->getContent())));
        }
    }

    public function renderView($data = array())
    {
        return $this->row->render();
    }

}
