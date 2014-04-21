<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap\Section;

use Toiee\HaikMarkdown\HaikMarkdown;
use Toiee\HaikMarkdown\Plugin\PluginCounter;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Row;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Column;
use Michelf\MarkdownInterface;

class SectionPlugin extends Plugin {

    protected static $PREFIX_CLASS_ATTRIBUTE = 'haik-plugin-section';

    const PlAY_MARK          = "{play}";

    protected $col_delimitor  = "\n====\n";

    protected $params;
    protected $body;
    
    protected $config = array();
    protected $counter;

    protected $content = '';

    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);
        $this->counter = PluginCounter::getInstance();
        $this->config = array(
            'section_style' => array(
              'color'            => '',
              'background-image' => '',
              'background-color' => '',
              'min-height'       => '',
            ),
            'container_style' => array(
              'vertical-align'   => '',
            ),
            'nojumbotron' => false,
            'align'       => '',
            'class'       => '',
        );
    }
    
    /**
     * convert call via HaikMarkdown :::{plugin-name(...):::
     * @params array $params
     * @params string $body when {...} was set
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    public function convert($params = array(), $body = '')
    {
        $called_class_name = get_called_class();
        $this->counter->inc($called_class_name);

        // set params
        $this->params = $params;
        $this->body = $body;
        
        $this->parseParams();
        $this->parseBody();
        
        return $this->renderView();
    }
    
    /**
     * parse params
     */
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

    /**
     * parse hash array params
     */
    protected function parseHashParams()
    {
        foreach ($this->params as $key => $value)
        {
            if (! is_array($value))
            {
                $value = trim($value);
            }
            switch ($key)
            {
                case 'align':
                    if (in_array($value, array('left', 'right', 'center')))
                    {
                        $this->addConfig('align', "text-{$value}");
                    }
                    break;
                // jumbotron
                case 'nojumbotron':
                case 'nojumbo':
                case 'no-jumbotron':
                case 'no-jumbo':
                    $this->addConfig('nojumbotron', true);
                    break;
                // vertical align
                case 'valign':
                    if (in_array($value, array('top', 'middle', 'bottom')))
                    {
                        $this->addConfig('container_style.vertical-align', $value);
                    }
                    break;
                // height
                case 'height':
                    if (is_numeric($value))
                    {
                        $value = $value.'px';
                    } 
                    $this->addConfig('section_style.min-height', $value);
                    break;
                // sectio class
                case 'class':
                    $this->addConfig('class', $value);
                    break;
                // background style
                case 'bg-image':
                case 'bg-color':
                    $name = str_replace('bg', 'background', $key);
                    $this->addConfig('section_style.' . $name, $value);
                    break;
                // font color
                case 'color':
                    $this->addConfig('section_style.color', $value);
                    break;
                // cols delimiter
                case 'delimiter':
                case 'delim':
                case 'separator':
                case 'sep':
                    if (! is_null($value) && $value !== '')
                    {
                      $this->col_delimitor = "\n" . $value . "\n";
                    }
                    break;
                // column
                case 'cols':
                case 'columns':
                    if (is_array($value) && ! $this->isHash($value))
                    {
                        foreach ($value as $column)
                        {
                            if (is_string($column) && $this->columnIsParsable($column))
                            {
                                $this->addColumns($column);
                            }
                            else if (is_array($column) && isset($column['span']) && $this->columnIsParsable($column['span']))
                            {
                                $column_obj = $this->createColumn($column['span']);
                                if (isset($column['offset'])) $column_obj->setOffsetWidth($column['offset']);
                                if (isset($column['class'])) $column_obj->addClassAttribute($column['class']);
                                if (isset($column['style'])) $column_obj->addStyleAttribute($column['style']);
                                $this->row[] = $column_obj;
                            }
                        }
                    }
                    else if ($this->columnIsParsable($value))
                    {
                        $this->addColumns($value);
                    }
                    else
                    {
                        try {
                            $columns = Yaml::parse('[' . $value . ']');
                            foreach ($columns as $column)
                            {
                                if ($this->columnIsParsable($column))
                                {
                                    $this->addColumns($column);
                                }
                            }
                        }
                        catch (ParseException $e) {}
                    }
                
            }
        }
    }

    /**
     * parse array params
     */
    protected function parseArrayParams()
    {
        foreach ($this->params as $param)
        {
            if (is_array($param))
            {
                foreach ($param as $key => $value)
                {
                    $value = trim($value);
                    switch($key)
                    {
                        // height
                        case 'height':
                            if (is_numeric($param['height']))
                            {
                                $value = $value.'px';
                            } 
                            $this->addConfig('section_style.min-height', $value);
                            break;
                        // section class
                        case 'class':
                            $this->addConfig('class', $value);
                            break;
                        // background style
                        case 'bg-image':
                        case 'bg-color':
                            $name = str_replace('bg', 'background', $key);
                            $this->addConfig('section_style.' . $name, $value);
                            break;
                        // font color
                        case 'color':
                            $this->addConfig('section_style.color', $value);
                            break;
                        // cols delimiter
                        case 'delimiter':
                        case 'delim':
                        case 'separator':
                        case 'sep':
                            if (! is_null($value) && $value !== '')
                            {
                                $this->col_delimitor = "\n" . $value . "\n";
                            }
                            break;
                    }
                }
            }
            else
            {
                switch($param)
                {
                    // align
                    case 'left':
                    case 'right':
                    case 'center':
                        $this->addConfig('align', "text-{$param}");
                        break;
                    // jumbotron
                    case 'nojumbotron':
                    case 'nojumbo':
                    case 'no-jumbotron':
                    case 'no-jumbo':
                        $this->addConfig('nojumbotron', true);
                        break;
                    // vertical align
                    case 'top':
                    case 'middle':
                    case 'bottom':
                        $this->addConfig('container_style.vertical-align', $param);
                        break;
                }
            }
        }
    }

    /**
     * parse body
     */
    protected function parseBody()
    {
        $body = $this->body;

        $cols = explode($this->col_delimitor, $body);
        $columns = array();
        if (count($cols) > 1)
        {
        		$col_width = (int)(Row::$COLUMN_SIZE / count($cols));
        		for ($i = 0; $i < count($cols); $i++)
        		{
        		    $column = new Column();
        		    $column->setColumnWidth($col_width);
                $column->setContent(trim($this->parser->transform($cols[$i])));
                $columns[$i] = $column;
        		}
            $row = new Row($columns);
        		$this->content = $row->render();
        }
        else 
        {
            $this->content = $this->parser->transform($body);
        }
        
        // !TODO swap play icon
    }
    
    /**
     * Add config data
     * @params string $key config key
     * @params string $val config val
     */
    protected function addConfig($key, $val)
    {
        $val = trim($val);

        if (strpos($key, '-image') !== false)
        {
            $val = 'url(' . $val . ')';
        }

        if (strpos($key, '.') === false)
        {
            $this->config[$key] = $val;
        }
        else
        {
           $keys = explode('.', $key);
           $this->config[$keys[0]][$keys[1]] = $val;
        }
    }
    
    /**
     * get style attribute
     * @params string $name which style
     * @return string converted style
     */
    protected function getStyleAttribute($name)
    {
        $style_name = $name.'_style';
        if ( ! isset($this->config[$style_name]))
        {
            return '';
        }

        $styles = array();
        foreach ($this->config[$style_name] as $key => $val)
        {
            if ($val !== '')
            {
              $val = e(rtrim($val, ';'));
              $styles[] = "{$key}:{$val}";
            }
        }
        $styles = array_filter($styles);
        return join(";", $styles);
    }

    /**
     * get class attribute
     */
    protected function getClassAttribute()
    {
        $classes = array();
        $classes[] = ($this->config['nojumbotron']) ? '' : 'jumbotron '. self::$PREFIX_CLASS_ATTRIBUTE . '-jumbotron';
        $classes[] = ($this->config['align']) ? $this->config['align'] : '';
        $classes[] = ($this->config['class']) ? e($this->config['class']) : '';
        $classes = array_filter($classes);

        return join(" ", $classes);
    }

    /**
     * render
     */
    public function renderView($data = array())
    {
        $section_style_attr   = $this->getStyleAttribute('section');
        $container_style_attr = $this->getStyleAttribute('container');
        $section_class_attr   = $this->getClassAttribute();

        // if first section plugin is called,  output section stylesheet
        $html  = $this->getPluginStylesheet();
        $html .= '
<div class="'. self::$PREFIX_CLASS_ATTRIBUTE . '">
  <div class="'. $section_class_attr . '" style="' . $section_style_attr . '">
    <div class="container" style="' . $container_style_attr . '">
      '. $this->content .'
    </div>
  </div>
</div>
';

        return $html;
    }
    
    /**
     * get section stylesheet when called first time
     *
     * @return string converted stylesheet
     */
    protected function getPluginStylesheet()
    {
        $called_class_name = get_called_class();
        if ($this->counter->get($called_class_name) > 1)
        {
            return '';
        }

        $style = '
<style>
  .##plugin_name## > div {
    display: table;
    width: 100%;
  }
  
  .##plugin_name##-jumbotron {
    margin-bottom: 0px;
    background-color: #fff;
  }
  
  .##plugin_name## > div > div.container {
    display: table-cell;
    width: 100%;
  }
</style>
';
        return str_replace('##plugin_name##', self::$PREFIX_CLASS_ATTRIBUTE, $style);
    }

}
