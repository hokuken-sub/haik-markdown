<?php
namespace Toiee\HaikMarkdown\Plugin\Pure\Units;

use Toiee\HaikMarkdown\Plugin\Pure\Plugin;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin as BootstrapColsPlugin;
use Toiee\HaikMarkdown\Plugin\Pure\Row;
use Toiee\HaikMarkdown\Plugin\Pure\Column;
use Toiee\HaikMarkdown\Plugin\Pure\Utility;
use Michelf\MarkdownInterface;

class UnitsPlugin extends BootstrapColsPlugin {

    public static $PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-units';
    public static $MAX_COLUMN_SIZE        = 120;

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
     * @param string $text
     * @return Toiee\HaikMarkdown\Plugin\Pure\Column
     * @see Toiee\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin::createColumn
     */
    protected function createColumn($text = '')
    {
        return new Column($text);
    }

    /**
     * Determine total unit size is valid?
     *
     * @return void
     */
    protected function validatesColumnSize()
    {
        $row_class_name = get_class($this->row);
        if ($this->getTotalColumnSize() > self::$MAX_COLUMN_SIZE)
        {
            $this->violateColumnSize = true;
        }
    }

    protected function getTotalColumnSize()
    {
        $total_columns = 0;
        foreach ($this->row as $column)
        {
            $unit_numerator = $column->getUnitNumerator();
            $unit_denominator = $column->getUnitDenominator();
            $multiple = (int)(self::$MAX_COLUMN_SIZE / $unit_denominator);
            $total_columns += $unit_numerator * $multiple;
        }
        return $total_columns;
    }

    protected function columnIsParsable($text)
    {
        return Column::isParsable($text);
    }

    /**
     * Set columns by body
     *
     * @see Toiee\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin::setColumnsByBody
     */
    protected function setColumnsByBody()
    {
        if (count($this->row) === 0)
        {
            // if parameter is not set then make cols with body
        	$data = explode($this->delimiter, $this->body);
        	$row_class_name = get_class($this->row);
    		$col_size = (int)(self::$MAX_COLUMN_SIZE / count($data));

    		$gcd = Utility::getGCD($col_size, self::$MAX_COLUMN_SIZE);
    		$unit_numerator = (int)($col_size / $gcd);
    		$unit_denominator = (int)(self::$MAX_COLUMN_SIZE / $gcd);
    		for ($i = 0; $i < count($data); $i++)
    		{
    		    $column = $this->createColumn()->setUnitSize($unit_numerator, $unit_denominator);
                $this->row[$i] = $column;
    		}
        }
    }

}
