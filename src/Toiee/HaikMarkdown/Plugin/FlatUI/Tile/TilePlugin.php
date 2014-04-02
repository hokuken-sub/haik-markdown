<?php
namespace Toiee\HaikMarkdown\Plugin\FlatUI\Tile;

use Toiee\HaikMarkdown\Plugin\FlatUI\Plugin;
/* use Toiee\HaikMarkdown\Plugin\Bootstrap\Thumbnails\ThumbnailsPlugin as BootstrapThumbnailsPlugin; */
use Toiee\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin as BootstrapColsPlugin;


class TilePlugin extends BootstrapColsPlugin {

    public static $PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-tile';

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
                $image = trim(strip_tags($top_line, '<a><img>'));
                $image = str_replace(
                           '<img', '<img class="tile-image big-illustration"',
                           strip_tags($image, '<img>')
                         );

                $column->thumbnail = $image;
                array_shift($lines);
            }

            $body = join("\n", $lines);


            $body = $this->parser->transform($body);
            if ( ! preg_match('{ <h[1-6][^>]*?class=".*?" }mx', $body))
            {
                $body = preg_replace('{ <h([1-6])(.*?>) }mx', '<h\1 class="tile-title"\2', $body);
            }
            $column->setContent($body);
            
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
            
            $content = '<div class="tile">'.$thumbnail.$content.'</div>';
            $column->setContent($content);
        }
        return parent::renderView($data);
    }

}
