<?php
namespace Toiee\HaikMarkdown\Plugin\FlatUI\Icon;

use Toiee\HaikMarkdown\Plugin\FlatUI\Plugin;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Icon\IconPlugin as BootstrapIconPlugin;

use Michelf\MarkdownInterface;

class IconPlugin extends BootstrapIconPlugin {

    protected static $BASE_CSS_CLASS_NAME    = '';
    protected static $PREFIX_CSS_CLASS_NAME  = 'fui-';
    
    protected $bootstrap_icon;

    /** @var string fui icon-name */
    protected $iconName;


    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);
        $this->bootstrap_icon = new BootstrapIconPlugin($parser);
    }


    function inline($params = array(), $body = '')
    {
        $offset = array_search('glyphicon', $params);
        if ($offset !== false)
        {
            array_splice($params, $offset, 1);
            return $this->bootstrap_icon->inline($params, $body);
        }

        $this->params = $params;
        $this->body = $body;

        return $this->parseParams()->renderView();
    }

}
