<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap\Panel;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Row;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Column;

class PanelPlugin extends Plugin {

    protected static $PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-panel';
    protected static $BASE_CSS_CLASS_NAME    = 'panel';
    protected static $PREFIX_CSS_CLASS_NAME  = 'panel-';
    protected static $DEFAULT_TYPE           = 'default';

    /** @var string type of panel */
    protected $type;

    protected $partialHead;
    protected $partialBody;
    protected $partialFooter;

    /** @var Row wrapper row */
    protected $row;

    protected $view = 'panel.template';

    public function __construct(\Michelf\MarkdownInterface $parser)
    {
        parent::__construct($parser);

        $this->type = self::$DEFAULT_TYPE;

        $this->partialHead = $this->partialBody = $this->partialFooter = '';
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
        $this->params = $params;
        $this->body = $body;
        
        $this->parseParams();

        $this->parseBody();

        return $this->renderView();
    }

    protected function parseParams()
    {
        $init_type = false;
        foreach ($this->params as $param)
        {
            $param = trim($param);

            switch ($param)
            {
                case 'default':
                case 'primary':
                case 'success':
                case 'info':
                case 'warning':
                case 'danger':
                    if ( ! $init_type)
                    {
                        $this->type = $param;
                        $init_type = true;
                    }
                    break;
                default:
                    if (Column::isParsable($param))
                    {
                        $this->row = new Row(array(new Column($param)));
                    }
            }
        }
    }

    protected function parseBody()
    {
        $body = $this->body;

        $partials = preg_split('{ \n+====\n+ }mx', $body, 3);

        $this->setPartials($partials);
    }

    protected function setPartials($partials)
    {
        switch (count($partials))
        {
            case 3:
                $this->setPartialFooter(array_pop($partials));
            case 2:
                $this->setPartialHead(array_shift($partials));
            default:
                $this->setPartialBody(array_shift($partials));
        }
    }

    protected function setPartialHead($partial)
    {
        if (trim($partial) === '') return;

        $partial = $this->parser->transform($partial);
        
        $partial = preg_replace_callback('/<h([1-6])(?:\s+([^>]+))?>/', array($this, '_replacePartialHead'), $partial);
        
        $this->partialHead = $partial;
    }

    protected function _replacePartialHead($matches)
    {
        $level = $matches[1];
        if ( ! isset($matches[2]))
        {
            return '<h' . $level . ' class="panel-title">';
        }

        $attributes = $matches[2];
        if ( ! preg_match('/\bclass\s*=\s*([\'"])(.*?)\1/i', $attributes))
        {
            return '<h' . $level . ' class="panel-title" ' . $attributes . '>';
        }
        $attributes = preg_replace_callback('/\A(.*)\bclass\s*=\s*([\'"])(.*?)\2(.*)\z/i', array($this, '_addHeadingClass'), $attributes);
        return '<h' . $level . ' ' . $attributes . '>';
    }

    protected function _addHeadingClass($matches)
    {
        $other_attr_head = $matches[1];
        $other_attr_tail = $matches[4];
        
        $class_attr = trim($matches[3]);
        $class_attr = $class_attr !== '' ? $class_attr : 'panel-title';

        return $other_attr_head . 'class="'. $class_attr.'"' . $other_attr_tail;
    }

    protected function setPartialBody($partial)
    {
        if (trim($partial) === '') return;

        $this->partialBody = $this->parser->transform($partial);
    }

    protected function setPartialFooter($partial)
    {
        if (trim($partial) === '') return;

        $this->partialFooter = $this->parser->transform($partial);
    }

    protected function createClassAttribute()
    {
        $classes = array();
        $classes[] = self::$PREFIX_CLASS_ATTRIBUTE;
        $classes[] = self::$BASE_CSS_CLASS_NAME;
        $classes[] = $this->getTypeClassName();
        
        return $this->classAttribute = trim(join(' ', $classes));
    }

    protected function getTypeClassName()
    {
        return self::$PREFIX_CSS_CLASS_NAME . $this->type;
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
