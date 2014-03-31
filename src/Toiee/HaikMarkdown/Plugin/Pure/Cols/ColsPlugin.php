<?php
namespace Toiee\HaikMarkdown\Plugin\Pure\Cols;

use Toiee\HaikMarkdown\Plugin\Pure\Plugin;
use Toiee\HaikMarkdown\Plugin\Pure\Units\UnitsPlugin;
use Toiee\HaikMarkdown\Plugin\Pure\Column;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Column as BootstrapColumn;

class ColsPlugin extends UnitsPlugin {

    protected function addColumns($text)
    {
        $column = $this->createColumn();
        if (preg_match(BootstrapColumn::$PARSABLE_REGEX, $text, $matches))
        {
            $unit_numerator = (int)$matches[1] * 2;
            $column->setUnitSize($unit_numerator);
            if ( isset($matches[3]) && $matches[3] !== '')
            {
                $column->addClassAttribute(str_replace('.', ' ', $matches[3]));
            }

            // offset column
            if (isset($matches[2]) && $matches[2] !== '')
            {
                $offset_unit_numerator = (int)$matches[2] * 2;
                $offset_column = $this->createColumn($offset_unit_numerator);
                $this->row[] = $offset_column;
            }
        }
        $this->row[] = $column;
    }

    protected function columnIsParsable($text)
    {
        return preg_match(BootstrapColumn::$PARSABLE_REGEX, $text);
    }

}
