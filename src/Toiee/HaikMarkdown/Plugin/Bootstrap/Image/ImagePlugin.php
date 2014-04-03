<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap\Image;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin;
use Michelf\MarkdownInterface;

class ImagePlugin extends Plugin {

    protected static $PREFIX_CLASS_ATTRIBUTE   = 'haik-plugin-image';
    protected static $PREFIX_CSS_CLASS_NAME    = 'img-';
    protected static $CSS_CLASS_NAME_FOR_BLOCK = 'img-responsive';
    protected static $DEFAULT_IMAGE            = 'http://placehold.jp/300x300.png';

    protected $imagePath;
    protected $type;
    protected $customClass;
    protected $altText;

    protected $block;


    public function __construct(\Michelf\MarkdownInterface $parser)
    {
        parent::__construct($parser);

        $this->imagePath = self::$DEFAULT_IMAGE;
        $this->type = $this->customClass = $this->altText = '';
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

/*         var_dump($this->parseParams()->renderView()); */
        return $this->parseParams()->renderView();
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

        return $this->parseParams()->renderView();
    }

    protected function parseParams()
    {
        if (count($this->params) === 0)
        {
            return $this;
        }

        $tmpParams = $this->params;
        $issetType = false;
        foreach ($this->params as $key => $param)
        {
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
            }

            if (preg_match('{ ^class=(.*)$ }mx', $param, $matches))
            {
                $param = $matches[1];
                $this->customClass = $param;
                unset($tmpParams[$key]);
                continue;
            }
        }

        if (count($tmpParams) > 0)
        {
            $this->imagePath = array_shift($tmpParams);
            $this->altText = join(' ', $tmpParams);
        }

        return $this;
    }

    public function createClassAttribute()
    {
        $classes = array();
        $classes[] = self::$PREFIX_CLASS_ATTRIBUTE;
        if ($this->block)
        {
            $classes[] = self::$CSS_CLASS_NAME_FOR_BLOCK;
        }
        $classes[] = $this->type;
        $classes[] = $this->customClass;

        return trim(join(' ', $classes));
    }

    public function renderView($data = array())
    {
        $class_attr = $this->createClassAttribute();
        return '<img src="'.e($this->imagePath).'" alt="'.e($this->altText).'" class="'.e($class_attr).'">';
    }
}
