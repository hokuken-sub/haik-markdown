<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap\Nav;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin;
use Symfony\Component\DomCrawler\Crawler;

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
        $crawler = new Crawler($this->contentBody);
        $lists = $crawler->filter('ul');

        $self = $this;
        $lists->each(function(Crawler $ul, $i) use ($self)
        {
            return $self->_setContentList($ul, $i);
        });

        $html = '';
        foreach ($lists as $ul)
        {
            $html .= $ul->ownerDocument->saveHTML($ul) . "\n";
        }
        $this->contentBody = $html;
    }

    protected function _setContentList(Crawler $ul, $i)
    {
        $self = $this;

foreach ($ul as $element) var_dump($element->ownerDocument->saveHTML());
        $ul->filter('ul ul')->each(function(Crawler $ul, $i) use ($self)
        {
            foreach ($ul as $element)
                $element->removeChild();
            return;
            foreach ($ul as $element) var_dump($element->ownerDocument->saveHTML());
            $self->_addClassNameOfNavbarDropdownMenu($ul, $i);
            foreach ($ul as $element) var_dump($element->ownerDocument->saveHTML());            
        });
foreach ($ul as $element) var_dump($element->ownerDocument->saveHTML());
        $ul->filter('li')->reduce(function(Crawler $li, $i) use ($self)
        {
            return $self->_excludeListItemWithoutLink($li, $i);
        });

        foreach ($ul as $element)
        {
            $class_attr = 'nav navbar-nav navbar-right';
            if ($element->hasAttribute('class'))
            {
                if (preg_match('{ \b dropdown-menu \b }x', $element->getAttribute('class')))
                    continue;
                $class_attr = rtrim($element->getAttribute('class')) . ' ' . $class_attr;
            }
            $element->setAttribute('class', $class_attr);
        }

    }

    protected function _addClassNameOfNavbarDropdownMenu(Crawler $ul, $i)
    {
        foreach ($ul as $element)
        {
            // ul.dropdown-menu
            $class_attr = 'dropdown-menu';
            if ($element->hasAttribute('class'))
            {
                $class_attr = rtrim($element->getAttribute('class')) . ' ' . $class_attr;
            }
            $element->setAttribute('class', $class_attr);
        }

        // li.dropdown
        foreach ($ul->parents() as $parent_li)
        {
            if ($parent_li->tagName !== 'li') continue;

            $class_attr = 'dropdown';
            if ($parent_li->hasAttribute('class'))
            {
                $class_attr = rtrim($parent_li->getAttribute('class')) . ' ' . $class_attr;
            }
            $parent_li->setAttribute('class', $class_attr);
        }

        // a.dropdown-toggle[data-toggle=dropdown]
        foreach ($ul->siblings() as $trigger)
        {
            if ($trigger->tagName !== 'a') continue;

            $class_attr = 'dropdown-toggle';
            $data_toggle_attr = 'dropdown';
            if ($trigger->hasAttribute('class'))
            {
                $class_attr = rtrim($trigger->getAttribute('class')) . ' ' . $class_attr;
            }
            $trigger->setAttribute('class', $class_attr);
            $trigger->setAttribute('data-toggle', $data_toggle_attr);
            $trigger->nodeValue .= ' <b class="caret"></b>';
        }

        // li.divider
        $ul->filter('li')->each(function(Crawler $li, $i)
        {
            if (count($li->filter('a')) > 0) return;

            $content = trim($li->text());
            if (preg_match('{ \A\s*-{3,}\s*\z }xs', $content))
            {
                foreach ($li as $element)
                {
                    $class_attr = 'divider';
                    if ($element->hasAttribute('class'))
                    {
                        $class_attr = rtrim($element->getAttribute('class')) . ' ' . $class_attr;
                    }
                    $element->setAttribute('class', $class_attr);
                    $element->nodeValue = '';
                }
            }
            return;
        });
    }

    protected function _excludeListItemWithoutLink(Crawler $li, $i)
    {
        if (count($li->filter('a')) === 0)
        {
            return false;
        }
        return $li;
    }

    protected function contentParagraphParser()
    {
        $crawler = new Crawler($this->contentBody);
        $paragraphs = $crawler->filter('p');

        if (count($paragraphs) === 0) return;

        foreach ($paragraphs as $element)
        {
            $class_attr = 'navbar-text';
            if ($element->hasAttribute('class'))
            {
                $class_attr = rtrim($element->getAttribute()) . ' ' . $class_attr;
            }
            $element->setAttribute('class', $class_attr);
        }

        $html = '';
        foreach ($paragraphs as $paragraph)
        {
            $html .= $paragraph->ownerDocument->saveHTML($paragraph) . "\n";
        }
        $this->contentBody = $html;
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

        $crawler = new Crawler($brand_title);

        // take top link
        $brand_link = $crawler->filter('a[href]')->eq(0);
        $brand_title = '';
        foreach ($brand_link as $element)
        {
            $class_attr = 'navbar-brand';
            if ($element->hasAttribute('class'))
            {
                $class_attr = rtrim($element->getAttribute('class')) . ' ' . $class_attr;
            }
            $element->setAttribute('class', $class_attr);
            $brand_title = $element->ownerDocument->saveHTML($element);
        }

        $this->hasBrandImage = !!count($brand_link->filter('img'));
        
        $this->brandTitle = $brand_title;

        return '';
    }

    protected function configActionButtonsParser()
    {
        $this->configBody = preg_replace_callback(
            '{
                (?:\A|\n)
                ACTION:\s*\n? #config name
                (
                    .*
                ) # $1: buttons markup
                (?:\nBRAND:|\z)
            }xs', array($this, '_setActionButtons'), $this->configBody);
    }

    protected function _setActionButtons($matches)
    {
        $whole_match = $matches[0];
        $html = $this->parser->transform($matches[1]);

        $crawler = new Crawler($html);
        $links = $crawler->filter('a');
        $base_class_attr = 'navbar-btn';
        $wrap_button_group = false;
        if (count($links) > 1)
        {
            $wrap_button_group = true;
        }
        else
        {
            $base_class_attr .= ' navbar-right';
        }

        foreach ($links as $element)
        {
            $class_attr = $base_class_attr;
            if ($element->hasAttribute('class'))
            {
                $class_attr = rtrim($element->getAttribute('class')) . ' ' . $class_attr;
            }
            $element->setAttribute('class', $class_attr);
        }

        $html = '';
        if ($wrap_button_group)
        {
            $html = '<div class="btn-group navbar-right">';
            foreach ($links as $element)
            {
                $html .= $element->ownerDocument->saveHTML($element) . "\n";
            }
            $html .= '</div>';
            $this->actionButtons = $html;
        }
        else
        {
            foreach ($links as $element)
            {
                $this->actionButtons = $element->ownerDocument->saveHTML($element);
            }
        }

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
