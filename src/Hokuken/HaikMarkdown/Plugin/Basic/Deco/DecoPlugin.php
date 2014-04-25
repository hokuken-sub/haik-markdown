<?php
namespace Hokuken\HaikMarkdown\Plugin\Basic\Deco;

use Hokuken\HaikMarkdown\Plugin\Plugin;

class DecoPlugin extends Plugin {

    protected $size = null;
    protected $strong = false;
    protected $underline = false;
    protected $italic = false;
    protected $color = null;
    protected $backgroundColor = null;

    /**
     * inline call via HaikMarkdown &plugin-name(...){...};
     * @params array $params
     * @params string $body when {...} was set
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    function inline($params = array(), $body = '')
    {
        $this->params = $params;
        $this->body = $body;
        $this->parseParams();

        $style_attribute = $this->createStyleAttribute();

        if ($this->strong)
        {
            $body = '<strong>'. $body .'</strong>';
        }

        return '<span class="haik-plugin-deco" style="'.htmlentities($style_attribute, ENT_QUOTES, 'UTF-8', false).'">'.$body.'</span>';
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
        $color = array();
        $ccnt = 0;
        foreach ($this->params as $value)
        {
            $value = trim($value);
            if( preg_match('/^\d+$/', $value) )
            {
                $this->size = $value.'px';
            }
            else if (preg_match('/^(\d|\.)/', $value))
            {
                $this->size = $value;
            }
            else if (preg_match('/small|medium|large/', $value))
            {
                $this->size = $value;
            }
            else if ($value=='bold' || $value=='b' )
            {
                $this->strong = true;
            }
            else if ($value=='underline' || $value=='u')
            {
                $this->underline = 'text-decoration:underline;';
            }
            else if ($value=='italic' || $value=='i')
            {
                $this->italic = 'font-style:italic;';
            }
            else if (preg_match('/^(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z-]+)$/i', $value))
            {
                $color[$ccnt] = $value;
                $ccnt++;
            }
            else if ($value === '')
            {
                $color[$ccnt] = 'inherit';
                $ccnt++;
            }
        }

        if (isset($color[0]) && $color[0]!='') $this->color = $color[0];
        if (isset($color[1]) && $color[1]!='') $this->backgroundColor = $color[1];

    }

    protected function parseHashParams()
    {
        foreach ($this->params as $key => $value)
        {
            $value = trim($value);
            switch ($key)
            {
                case 'size':
                    $this->size = is_numeric($value) ? "{$value}px" : $value;
                    break;
                case 'b':
                case 'bold':
                case 'strong':
                    $this->strong = true;
                    break;
                case 'i':
                case 'italic':
                    $this->italic = true;
                    break;
                case 'u':
                case 'underline':
                    $this->underline = true;
                    break;
                case 'color':
                    $this->color = $value;
                    break;
                case 'bg-color':
                case 'background-color':
                    $this->backgroundColor = $value;
                    break;
            }
        }
    }

    protected function createStyleAttribute()
    {
        $props = array();
        if ($this->size) $props['font-size'] = $this->size;
        if ($this->color) $props['color'] = $this->color;
        if ($this->backgroundColor) $props['background-color'] = $this->backgroundColor;
        if ($this->underline) $props['text-decoration'] = 'underline';
        if ($this->italic) $props['font-style'] = 'italic';
        return join(';', array_map(function($key, $value)
        {
            return $key . ':' . $value;
        }, array_keys($props), array_values($props)));
    }

}
