<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap\Alert;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin;
use Michelf\MarkdownInterface;

class AlertPlugin extends Plugin {

    protected static $PREFIX_CLASS_ATTRIBUTE     = 'haik-plugin-alert';
    protected static $BASE_CSS_CLASS_NAME        = 'alert';
    protected static $PREFIX_CSS_CLASS_NAME      = 'alert-';
    protected static $DEFAULT_TYPE               = 'warning';
    protected static $DISMISSABLE_CSS_CLASS_NAME = 'alert-dismissable';

    protected $classAttribute;

    /** @var string type of alert */
    protected $type;

    /** @var boolean the alert block dismissable */
    protected $dismissable;

    protected $customCssClassName;

    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);

        $this->classAttribute = '';
        $this->type = self::$DEFAULT_TYPE;
        $this->dismissable = false;
        $this->customCssClassName = '';
    }
    public function convert($params = array(), $body = '')
    {
        $this->params = $params;
        $this->body = $body;

        $this->parseParams();

        return $this->renderView();
    }

    protected function parseParams()
    {
        foreach ($this->params as $param)
        {
            switch ($param)
            {
                case 'success':
                case 'info'   :
                case 'warning':
                case 'danger' :
                    $this->type = $param;
                    break;
                case 'close':
                    $this->dismissable = true;
                    break;
                default:
                    $this->customCssClassName = trim($this->customCssClassName . ' ' . trim($param));
            }
        }
        
    }

    /**
     * @see Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin.php
     */
    public function renderView($data = array())
    {
        $close_button = $this->createCloseButton();
        $content = $this->parser->transform($this->body);
        $class_attribute = $this->createClassAttribute();
        
        return '<div class="'. e($class_attribute) .'">'.$close_button.$content.'</div>';
    }

    protected function createClassAttribute()
    {
        $classes = array();
        $classes[] = self::$PREFIX_CLASS_ATTRIBUTE;
        $classes[] = self::$BASE_CSS_CLASS_NAME;
        $classes[] = $this->getTypeClassName();
        if ($this->dismissable)
        {
            $classes[] = self::$DISMISSABLE_CSS_CLASS_NAME;
        }
        $classes[] = $this->customCssClassName;
        
        return $this->classAttribute = trim(join(' ', $classes));
    }

    protected function createCloseButton()
    {
        if ( ! $this->dismissable) return '';
        return '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    }

    protected function getTypeClassName()
    {
        return self::$PREFIX_CSS_CLASS_NAME . $this->type;
    }
}
