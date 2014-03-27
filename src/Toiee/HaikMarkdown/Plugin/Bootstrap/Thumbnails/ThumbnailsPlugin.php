<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap\Thumbnails;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Column;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Row;

class ThumbnailsPlugin extends ColsPlugin {

    public static $PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-thumbnails';

    /**
     * Parse columns content's markdown
     *
     * @see Toiee\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin::parseColumns
     */
    protected function parseColumns()
    {
        foreach ($this->row as $i => $column)
        {
            $lines = preg_split('{ \n+ }mx', trim($column->getContent()));

            $top_line = $this->parser->transform($lines[0]);
            if (strpos($top_line, '<img') !== FALSE)
            {
                $column->thumbnail = trim(strip_tags($top_line, '<a><img>'));
                array_shift($lines);
            }

            $body = join("\n", $lines);
            $column->setContent($this->parser->transform($body));
            
            $this->row[$i] = $column;
        }
    }

    /**
     * Render view
     *
     * @see Toiee\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin::renderView
     */
    public function renderView($data = array())
    {
        foreach ($this->row as $column)
        {
            $thumbnail = isset($column->thumbnail) ? $column->thumbnail : '';
            $content = $column->getContent();
            
            $content = '<div class="thumbnail">'.$thumbnail.'<div class="caption">'.$content.'</div></div>';
            $column->setContent($content);
        }
        return parent::renderView($data);
    }

}
