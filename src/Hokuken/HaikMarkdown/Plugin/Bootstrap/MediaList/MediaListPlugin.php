<?php
namespace Hokuken\HaikMarkdown\Plugin\Bootstrap\MediaList;

use Hokuken\HaikMarkdown\Plugin\Bootstrap\Plugin;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Carousel\CarouselPlugin;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Row;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Column;

class MediaListPlugin extends CarouselPlugin {

    const DEFAULT_IMAGE = '<img class="media-object" src="http://placehold.jp/80x80.png" alt="">';

    protected function initialize()
    {
        $this->view = 'media_list.template';
    }

    protected function adjustImage($html)
    {
        return str_replace(
                          '<img', '<img class="media-object"',
                          strip_tags($html, '<img>')
                          );
    }

    protected function adjustHeading($html)
    {
        if ( ! preg_match('{ <h[1-6][^>]*?class=".*?" }mx', $html))
        {
            return preg_replace('{ <h([1-6])(.*?>) }mx', '<h\1 class="media-heading"\2', $html);
        }
        else
        {
            return $html;
        }
    }

    /**
     * Adjust item data
     *
     * @param mixed $itemData data of item
     * @return mixed adjusted item data
     */
    protected function adjustData($itemData)
    {
        extract($itemData['body_data']);

        if ( ! isset($elements[$max_line])) return $itemData;

        $html = $this->parser->transform($elements[$max_line]);
        if ( ! $imageSet && preg_match('{ <img\b.*?> }mx', $html))
        {
            $itemData['image'] = str_replace(
                                      '<img', '<img class="media-object"',
                                      strip_tags($html, '<img>')
                                      );
            $itemData['align'] = 'pull-right';

            unset($itemData['body_data']['elements'][$max_line]);
        }
        return $itemData;
    }

    /**
     * check params
     */
    protected function checkParams()
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

    /**
     * parse hash array params
     */
    protected function parseHashParams()
    {
        foreach ($this->params as $key => $value)
        {
            $value = trim($value);
            switch ($key)
            {
                case 'span':
                    if (Column::isParsable($value))
                    {
                        $this->row = new Row(array(new Column($value)));
                    }
                    break;
            }
        }
    }

    /**
     * parse array params
     */
    protected function parseArrayParams()
    {
        foreach ($this->params as $i => $param)
        {
            if (Column::isParsable($param))
            {
                $this->row = new Row(array(new Column($param)));
            }
        }
    }

}
