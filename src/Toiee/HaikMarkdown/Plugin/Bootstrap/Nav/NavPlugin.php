<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap\Nav;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin;

class NavPlugin extends Plugin {

    const PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-nav';
    const BASE_CSS_CLASS_NAME = 'navbar';
    const PREFIX_CSS_CLASS_NAME = 'navbar-';
    const CONFIG_DELIMITER_REGEX = '/ (?:\A|\n)\*(?:\s+\*){1,}\s*(\n|\z) /xs';
    const DEFAULT_FIXED_TO = 'top';
    const DEFAULT_TYPE = 'default';

    /** @var string Text of config split from body */
    protected $configBody;

    /** @var array Order of config parser methods */
    protected $configParsers = array(
        'configBrandTitleParser' => 10,
        'configActionButtonsParser' => 20,
    );

    /** @var string Text of content split from body */
    protected $contentBody;

    protected $contentParsers = array(
        'contentListParser' => 10,
        'contentParagraphParser' => 20,
    );

    /** @var string HTML of action buttons */
    protected $actionButtons;

    /** @var boolean wrap action buttons by .btn-group */
    protected $wrapActionButtons;

    /** @var string HTML of the title of brand */
    protected $brandTitle;

    /** @var boolean has brand image */
    protected $hasBrandImage;

    /** @var string type of nav */
    protected $type;

    /** @var boolean Fixed to top or bottom? */
    protected $fixed;

    /** @var string Where the nav is fixed to? */
    protected $fixedTo;

    protected $forResponsive;

    protected $view = 'nav.template';

    public function __construct(\Michelf\MarkdownInterface $parser)
    {
        parent::__construct($parser);
        $this->initialize();
    }

    protected function initialize()
    {
        $this->actionButtons = $this->brandTitle = '';
        $this->hasBrandImage = $this->wrapActionButtons = false;
        $this->fixed = $this->forResponsive = true;
        $this->fixedTo = self::DEFAULT_FIXED_TO;
        $this->type = self::DEFAULT_TYPE;

		asort($this->configParsers);
		asort($this->contentParsers);
    }

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
        $set_static = false;
        foreach ($this->params as $param)
        {
            $param = trim($param);
            switch ($param)
            {
                case 'bottom':
                case 'top':
                    if ( ! $set_static)
                    {
                        $this->fixed = true;
                        $this->fixedTo = $param;
                    }
                    break;
                case 'static':
                    $this->fixed = false;
                    $set_static = true;
                    break;
                case 'inverse':
                case 'default':
                    $this->type = $param;
                    break;
                case 'non-responsive':
                    $this->forResponsive = false;
            }
        }
    }

    protected function parseBody()
    {
        $called_class = \get_called_class();
        $delimiter = $called_class::CONFIG_DELIMITER_REGEX;
        if (preg_match($delimiter, $this->body))
        {
            list($this->contentBody, $this->configBody) = preg_split($delimiter, $this->body, 2);
            $this->parseConfigBody();
        }
        else
        {
            $this->contentBody = $this->body;
        }

        $this->parseContentBody();
    }

    protected function parseContentBody()
    {
        $this->contentBody = $this->parser->transform($this->contentBody);
        foreach ($this->contentParsers as $parser => $order)
        {
            $this->$parser();
        }
    }

    protected function contentListParser()
    {
        $this->contentBody = preg_replace_callback(
            '{
                <ul(?:\s+.+?)?>
                    .*
                </ul>
            }xs', array($this, '_setContentList'), $this->contentBody);
    }

    protected function _setContentList($matches)
    {
        $whole_match = $matches[0];
        
        $html = preg_replace_callback('/<ul(?:\s+(.*?))?>/',
                                      array($this, '_addClassNameOfNavbarList'),
                                      $whole_match, 1);

/*
        $html = preg_replace_callback('{
                (<li(?:.*?)>)     # $1: parent list item
                    (.+?)         # $2: trigger
                    (<ul(?:.*?)>) # $3: .dropdown-menu
                        (.*?)     # $4: ul contents
                    </ul>
                </li>
            }xs',
            array($this, '_addClassNameOfNavbarDropdownMenu'), $html);
*/
        $html = preg_replace_callback('{
                (<li(?:.*?)>)     # $1: parent list item
                    (.*?)
                    (<ul(?:.*?)>) # $3: .dropdown-menu
                        (.*?)     # $4: ul contents
                    </ul>\s*
                </li>
            }xs',
            array($this, '_addClassNameOfNavbarDropdownMenu'), $html);

        // exclude list item without link
        $html = preg_replace_callback('{ <li>(.*?)</li> }xs',
                                      array($this, '_excludeListItemWithoutLink'),
                                      $html);

        return $html;
    }

    protected function _addClassNameOfNavbarList($matches)
    {
        $attrs = isset($matches[1]) ? $matches[1] : '';
        if (preg_match('/\bclass\s*=\s*(\'|")(.*)?\1/', $attrs, $inner_matches))
        {
            $attrs = str_replace($inner_matches[0], '', $attrs);
            $class_attr = $inner_matches[2] . ' nav navbar-nav navbar-right';
            $attrs .= 'class="'. e($class_attr) . '"';
        }
        else
        {
            $attrs .= 'class="nav navbar-nav navbar-right"';
        }
        return '<ul '.$attrs.'>';
    }

    protected function _addClassNameOfNavbarDropdownMenu($matches)
    {
    var_dump('matches', $matches);
        $whole_match = $matches[0];
        $parent_li_open_tag = $matches[1];
        $trigger_a_tag = $matches[2];
        $dropdown_ul_open_tag = $matches[3];
        $ul_contents_tag = $matches[4];

        $parent_li_open_tag = preg_replace_callback('/<li(?:\s+(.*?))?>/',
                                                    array($this, '_addClassNameOfNavbarDropdownMenuParentListItem'),
                                                    $parent_li_open_tag);
        $trigger_a_tag = preg_replace_callback('/<a(?:\s+(.*?))?>/',
                                               array($this, '_addAttributesOfNavbarDropdownMenuTrigger'),
                                               $trigger_a_tag, 1);
        $trigger_a_tag = preg_replace_callback('{ </a> }xs',
                                               array($this, '_addCaretOfNavbarDropdownMenuTrigger'),
                                               $trigger_a_tag);
        $dropdown_ul_open_tag = preg_replace_callback('/<ul(?:\s+(.*?))?>/',
                                                      array($this, '_addClassNameOfNavbarDropdownMenuList'),
                                                      $dropdown_ul_open_tag);
        $ul_contents_tag = preg_replace_callback('{
                                                  <li>
                                     				^[ ]{0,3}	# Leading space
                                    				([-*_])		# $1: First marker
                                    				(?>			# Repeated marker group
                                    					[ ]{0,2}	# Zero, one, or two spaces.
                                    					\1			# Marker character
                                    				){2,}		# Group repeated at least twice
                                    				[ ]*		# Tailing spaces
                                                 </li>
                                                  }xs',
                                                 array($this, '_addClassNameOfNavbarDropdownMenuDivider'),
                                                 $ul_contents_tag);

        return <<< EOD
{$parent_li_open_tag}
  {$trigger_a_tag}
  {$dropdown_ul_open_tag}
    {$ul_contents_tag}
  </ul>
</li>
EOD;
    }

    protected function _addClassNameOfNavbarDropdownMenuParentListItem($matches)
    {
        $attrs = isset($matches[1]) ? $matches[1] : '';
        if (preg_match('/\bclass\s*=\s*(\'|")(.*)?\1/', $attrs, $inner_matches))
        {
            $attrs = str_replace($inner_matches[0], '', $attrs);
            $class_attr = $inner_matches[2] . ' dropdown';
            $attrs .= 'class="'. e($class_attr) . '"';
        }
        else
        {
            $attrs .= 'class="dropdown"';
        }
        return '<li '.$attrs.'>';
    }

    protected function _addAttributesOfNavbarDropdownMenuTrigger($matches)
    {
        $attrs = isset($matches[1]) ? $matches[1] : '';
        if (preg_match('/\bclass\s*=\s*(\'|")(.*)?\1/', $attrs, $inner_matches))
        {
            $attrs = str_replace($inner_matches[0], '', $attrs);
            $class_attr = $inner_matches[2] . ' dropdown-toggle';
            $attrs .= ' class="'. e($class_attr) . '"';
        }
        else
        {
            $attrs .= ' class="dropdown-toggle"';
        }

        if ( ! preg_match('/\bdata-toggle\s*=\s*(\'|")dropdown\1/', $attrs, $inner_matches))
        {
            $attrs .= ' data-toggle="dropdown"';
        }

        return '<a '.$attrs.'>';
    }

    protected function _addCaretOfNavbarDropdownMenuTrigger($matches)
    {
        return ' <b class="caret"></b></a>';
    }

    protected function _addClassNameOfNavbarDropdownMenuList($matches)
    {
        $attrs = isset($matches[1]) ? $matches[1] : '';
        if (preg_match('/\bclass\s*=\s*(\'|")(.*)?\1/', $attrs, $inner_matches))
        {
            $attrs = str_replace($inner_matches[0], '', $attrs);
            $class_attr = $inner_matches[2] . ' dropdown-menu';
            $attrs .= 'class="'. e($class_attr) . '"';
        }
        else
        {
            $attrs .= 'class="dropdown-menu"';
        }
        return '<ul '.$attrs.'>';        
    }

    protected function _addClassNameOfNavbarDropdownMenuDivider($matches)
    {
        return '<li class="divider"></li>';
    }

    protected function _excludeListItemWithoutLink($matches)
    {
        $li_content = $matches[1];
        if ( ! preg_match('{ <a.*?href.*?</a> }xs', $li_content))
        {
            return '';
        }
        return $matches[0];
    }

    protected function contentParagraphParser()
    {
        $this->contentBody = preg_replace_callback(
            '{
                <p>(.*)</p>
            }xs', function($matches)
        {
            return '<p class="navbar-text">' . $matches[1] . '</p>';
        }, $this->contentBody);
    }

    protected function parseConfigBody()
    {
        foreach ($this->configParsers as $parser => $order)
        {
            $this->$parser();
        }
    }

    protected function configBrandTitleParser()
    {
        $this->configBody = preg_replace_callback(
            '{
                ^
                    BRAND:
                    [ ]*
                    (.*)
                $
            }mx', array($this, '_setBrandTitle'), $this->configBody);
    }

    protected function _setBrandTitle($matches)
    {
        $whole_match = $matches[0];
        $brand_title = $this->parser->transform($matches[1]);
        $brand_title = strip_tags($brand_title, '<img><a>');

        // has link?
        if ( ! preg_match('/<a\b.*?\bhref\b.*?>.*?<\/a>/', $brand_title, $matches))
        {
            return;
        }
        // take only first link
        $brand_title = $matches[0];

        // has image?
        if (preg_match('/<img\s*(.*?)>/', $brand_title, $matches))
        {
            $this->hasBrandImage = true;
        }

        // set navbar-brand class
        $brand_title = preg_replace_callback('/<a(?:\s+(.*?))?>/', function($matches)
        {
            $attrs = $matches[1];
            if (preg_match('/\bclass\s*=\s*(\'|")(.*)?\1/', $attrs, $inner_matches))
            {
                $attrs = str_replace($inner_matches[0], '', $attrs);
                $class_attr = $inner_matches[2] . ' navbar-brand';
                $attrs .= ' class="'. e($class_attr) . '"';
            }
            else
            {
                $attrs .= ' class="navbar-brand"';
            }
            return '<a ' . $attrs . '>';
        }, $brand_title);

        $this->brandTitle = $brand_title;

        return '';
    }

    protected function configActionButtonsParser()
    {
        $this->configBody = preg_replace_callback(
            '{
                (?:\A|\n)
                ACTION:\s*\n? #config name
                (?:.*?)
                (
                    (?:
                        <a\b.+?>.*?</a>
                        (?:.*)
                    )+
                ) # $1: buttons
                (?:.*?)
                (?:\n|\z)
            }xs', array($this, '_setActionButtons'), $this->configBody);
    }

    protected function _setActionButtons($matches)
    {
        $whole_match = $matches[0];
        $html = $this->parser->transform($matches[1]);
        $links = array();
        $links_count = preg_match_all('/<a\b.+?>.*?<\/a>/', $html, $matches);
        if ($links_count > 1)
        {
            $this->wrapActionButtons = true;
        }
        foreach ($matches[0] as $link)
        {
            $links[] = $link;
        }
        $links = join("\n", $links);

        // add class name
        $this->actionButtons = preg_replace_callback('/<a(?:\s+(.*?))?>/', array($this, '_addClassNameOfNavbarButton'), $links);

        return '';
    }

    protected function _addClassNameOfNavbarButton($matches)
    {
        $whole_match = $matches[0];
        $attrs = $matches[1];
        if (preg_match('/\bclass\s*=\s*(\'|")(.*)?\1/', $attrs, $inner_matches))
        {
            $attrs = str_replace($inner_matches[0], '', $attrs);
            $class_attr = $inner_matches[2] . ' navbar-btn';
        }
        else
        {
            $class_attr = 'navbar-btn';
        }
        if ( ! $this->wrapActionButtons)
        {
            $class_attr .= ' navbar-right';
        }
        $attrs .= 'class="'. e($class_attr) . '"';
        return '<a ' . $attrs . '>';
    }

    protected function getTypeClassName()
    {
        return self::PREFIX_CSS_CLASS_NAME . $this->type;
        
    }

    protected function getPositionClassName()
    {
        if ($this->fixed)
        {
            return self::PREFIX_CSS_CLASS_NAME . 'fixed-' . $this->fixedTo;
        }
        else
        {
            return self::PREFIX_CSS_CLASS_NAME . 'static-top';
        }
    }

    protected function createClassAttribute()
    {
        $classes = [];
        $classes[] = self::PREFIX_CLASS_ATTRIBUTE;
        $classes[] = self::BASE_CSS_CLASS_NAME;
        $classes[] = $this->getTypeClassName();
        $classes[] = $this->getPositionClassName();

        $classes = array_filter($classes);

        return join(' ', $classes);
    }

}
