<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap\Carousel;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Row;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Column;
use Michelf\MarkdownInterface;

class CarouselPlugin extends Plugin {

    const DEFAULT_IMAGE = '<img src="http://placehold.jp/900x500.png" alt="">';

    protected $items = array();
    protected $options = array();
    protected $view;

    protected $params;
    protected $body;
    protected $cols;

    /** @var Row wrapper row */
    protected $row;

    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);
        $this->initialize();
    }

    protected function initialize()
    {
        $this->view = 'carousel.template';
        $this->options = array(
            'indicatorsSet' => true,
            'controlsSet'   => true,
        );
    }

    public function convert($params = array(), $body = '')
    {
        $this->params = $params;
        $this->body   = $body;

        $item_bodies = preg_split('/^={4}$/m', $body);

        foreach ($item_bodies as $i => $item_body)
        {
            $item = $this->createItemData($item_body);
            if ($item !== FALSE)
            {
                $this->items[] = $item;
            }
        }

        $this->checkParams();

        $class_name = get_called_class();
        return $this->renderView(array(
            'defaultImage' => $class_name::DEFAULT_IMAGE
        ));
    }
    
    protected function createItemData($body)
    {
        $body = trim($body);
        if ($body === '') return FALSE;
        
        $data = array();

        $elements = preg_split('{ \n+ }mx', $body);
        $line_count = count($elements);
        $max_line = $line_count - 1;

        $imageSet = $headingSet = false;
        
        
        
        // 最初の2行のみ
        for ($line = 0; $line < 2 && $line < $line_count; $line++)
        {
            $html = $this->parser->transform($elements[$line]);

            //画像をセット
            if ( ! $imageSet && preg_match('{ <img\b.*?> }mx', $html))
            {
                $data['image'] = $this->adjustImage($html);;
                $imageSet = true;
                unset($elements[$line]);
            }
            //見出しをセット
            else if ( ! $headingSet)
            {
                if (preg_match('{ <h }mx', $html))
                {
                    $data['heading'] = $this->adjustHeading($html);
                    $headingSet = true;
                    unset($elements[$line]);
                }
                break;
            }
        }

        $data['body_data'] = compact('elements', 'line_count', 'max_line', 'imageSet', 'headingSet');
        $data = $this->adjustData($data);
        if (isset($data['body_data']))
        {
            extract($data['body_data']);
            unset($data['body_data']);
        }

        // 残りをparse
        $data['body'] = $this->parser->transform(join("\n", $elements));
        
        foreach ($data as $key => $value)
        {
            if (is_string($value))
                $data[$key] = trim($value);
        }

        return $data;
    }

    protected function adjustImage($html)
    {
        return strip_tags($html, '<img>');
    }

    protected function adjustHeading($html)
    {
        return preg_replace('{ <h([1-6])(.*?>)(.*?)(</h\1>) }mx', '<h3\2\3</h3>', $html);
    }

    /**
     * Adjust item data
     *
     * @param mixed $itemData data of item
     * @return mixed adjusted item data
     */
    protected function adjustData($itemData)
    {
        unset($itemData['body_data']);
        return $itemData;
    }

    protected function checkParams()
    {
        if (count($this->items) === 1)
            $this->options['indicatorsSet'] = $this->options['controlsSet'] = false;

        $this->parseParams();
    }

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
        foreach ($this->params as $i => $param)
        {
            switch ($param)
            {
                case 'nobutton':
                    $this->options['indicatorsSet'] = $this->options['controlsSet'] = false;
                    break;
                case 'noindicator':
                    $this->options['indicatorsSet'] = false;
                    break;
                case 'noslidebutton':
                    $this->options['controlsSet'] = false;
                    break;
                default:
                    if (Column::isParsable($param))
                    {
                        $this->row = new Row(array(new Column($param)));
                    }
            }
        }
    }

    protected function parseHashParams()
    {
        $set_buttons = null;
        foreach ($this->params as $key => $value)
        {
            $enabled = null;
            if (is_bool($value))
            {
                $enabled = $value;
            }
            else if (is_string($value))
            {
                $value = trim($value);
                $enabled = ! in_array($value, ['disabled', 'none']);
            }

            switch ($key)
            {
                case 'buttons':
                    if ($enabled)
                    {
                        $this->options['indicatorsSet'] = $this->options['controlsSet'] = true;
                    }
                    else
                    {
                        $this->options['indicatorsSet'] = $this->options['controlsSet'] = false;
                    }
                    $set_buttons = true;
                    break;
                case 'indicator':
                    if ($set_buttons) break;
                    if ($enabled)
                    {
                        $this->options['indicatorsSet'] =  true;
                    }
                    else
                    {
                        $this->options['indicatorsSet'] =  false;
                    }
                    break;
                case 'slidebuttons':
                case 'controls':
                    if ($set_buttons) break;
                    if ($enabled)
                    {
                        $this->options['controlsSet'] = true;
                    }
                    else
                    {
                        $this->options['controlsSet'] = false;
                    }
                    break;
                case 'span':
                    if (Column::isParsable($value))
                    {
                        $this->row = new Row(array(new Column($value)));
                    }
            }
        }
    }

    public function renderView($data = array())
    {
        $html = parent::renderView($data);

        if ($this->row)
        {
            $this->row[0]->setContent($html);
            return $this->row->render();
        }
        return $html;
    }

}
