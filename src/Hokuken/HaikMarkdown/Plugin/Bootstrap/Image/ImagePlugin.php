<?php
namespace Hokuken\HaikMarkdown\Plugin\Bootstrap\Image;

use Hokuken\HaikMarkdown\Plugin\Bootstrap\Plugin;
use Michelf\MarkdownInterface;

class ImagePlugin extends Plugin {

    protected static $PREFIX_CLASS_ATTRIBUTE   = 'haik-plugin-image';
    protected static $PREFIX_CSS_CLASS_NAME    = 'img-';
    protected static $CSS_CLASS_NAME_FOR_BLOCK = 'img-responsive';
    protected static $DEFAULT_IMAGE            = 'http://placehold.jp/300x300.png';

    protected $imagePath;
    protected $type;
    protected $customClass;
    protected $float;
    protected $titleText;
    protected $altText;
    protected $style;

    protected $block;


    public function __construct(\Michelf\MarkdownInterface $parser)
    {
        parent::__construct($parser);

        $this->imagePath = self::$DEFAULT_IMAGE;
        $this->type = $this->customClass = $this->float = '';
        $this->titleText = $this->altText = $this->style = '';
        $this->block = false;
    }
    /**
     * inline call via HaikMarkdown &plugin-name(...);
     * @params array $params
     * @params string $body when {...} was set
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    public function inline($params = array(), $body = '')
    {
        $this->params = $params;
        $this->body = $body;

        return $this->setAltText()->parseParams()->renderView();
    }

    /**
     * convert call via HaikMarkdown {#plugin-name}
     * @params array $params
     * @params string $body when :::\n...\n::: was set
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    public function convert($params = array(), $body = '')
    {
        $this->block = true;

        $this->params = $params;
        $this->body = $body;

        return $this->setAltText()->parseParams()->renderView();
    }

    /**
     * Set alt text from body
     *
     * @return $this for method chain
     */
    protected function setAltText()
    {
        $this->titleText = $this->altText = htmlentities(strip_tags($this->body), ENT_QUOTES, 'UTF-8', false);
        return $this;
    }

    protected function parseParams()
    {
        if (count($this->params) === 0)
        {
            return $this;
        }

        if ($this->isHash($this->params))
        {
            $this->parseHashParams();
        }
        else
        {
            $this->parseArrayParams();
        }
        return $this;
    }

    protected function parseArrayParams()
    {
        $tmpParams = $this->params;
        $issetType = false;
        $issetFloat = false;
        foreach ($this->params as $key => $param)
        {
            if (is_array($param))
            {
                if (isset($param['class']))
                {
                    $this->customClass = $param['class'];
                }
                unset($tmpParams[$key]);
                continue;
            }
            $param = trim($param);

            switch ($param)
            {
                case 'rounded':
                case 'thumbnail':
                case 'circle':
                    if ( ! $issetType)
                    {
                        $this->type = self::$PREFIX_CSS_CLASS_NAME . $param;
                        $issetType = true;
                    }
                    unset($tmpParams[$key]);
                    continue 2;
                    break;
                case 'left':
                case 'right':
                    if ( ! $issetFloat && $this->block)
                    {
                        $this->float = 'pull-' . $param;
                        $issetFloat = true;
                    }
                    unset($tmpParams[$key]);
                    continue 2;
                    break;
            }
        }

        if (count($tmpParams) > 0)
        {
            $this->imagePath = array_shift($tmpParams);
            $this->titleText = join(' ', $tmpParams);
        }
    }

    public function parseHashParams()
    {
        foreach ($this->params as $key => $param)
        {
            $param = trim($param);

            switch ($key)
            {
                case 'url':
                case 'path':
                case 'image':
                    $this->imagePath = $param;
                    break;
                case 'type':
                    if (in_array($param, ['rounded', 'thumbnail', 'circle']))
                    {
                        $this->type = self::$PREFIX_CSS_CLASS_NAME . $param;
                    }
                    break;
                case 'pull':
                case 'align':
                    if ($this->block)
                    {
                        $this->float = 'pull-' . $param;
                    }
                    break;
                case 'class':
                    $this->customClass = $param;
                    break;
                case 'title':
                    $this->titleText = $param;
            }
        }
    }

    public function createClassAttribute()
    {
        $classes = array();
        $classes[] = self::$PREFIX_CLASS_ATTRIBUTE;
        if ($this->block)
        {
            $classes[] = self::$CSS_CLASS_NAME_FOR_BLOCK;
            $classes[] = $this->float;
        }
        $classes[] = $this->type;
        $classes[] = $this->customClass;

        return trim(join(' ', $classes));
    }

    public function createImageStyle()
    {
        if ($this->float === 'pull-left')
        {
            $this->style = 'style="margin: 0 15px 15px 0;"';
        }
        elseif ($this->float === 'pull-right')
        {
            $this->style = 'style="margin: 0 0 15px 15px;"';
        }

        return $this->style;
    }

    public function renderView($data = array())
    {
        $class_attr = $this->createClassAttribute();
        $style = $this->createImageStyle();
        $image_path = htmlentities($this->imagePath, ENT_QUOTES, 'UTF-8', false);
        $alt_text = htmlentities($this->altText, ENT_QUOTES, 'UTF-8', false);
        $title_text = htmlentities($this->titleText, ENT_QUOTES, 'UTF-8', false);
        $class_attr = htmlentities($class_attr, ENT_QUOTES, 'UTF-8', false);
        return '<img src="'.$image_path.'" alt="'.$alt_text.'" title="'.$title_text.'" class="'.$class_attr.'" '.$style.'>';
    }
}
