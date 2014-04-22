<?php
namespace Hokuken\HaikMarkdown\Plugin\FlatUI\Button;

use Hokuken\HaikMarkdown\Plugin\FlatUI\Plugin;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Button\ButtonPlugin as BootstrapButton;

use Michelf\MarkdownInterface;

class ButtonPlugin extends BootstrapButton {

    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);
    }

    public function parseParams()
    {
        $params = $this->params;
        if (count($params) > 0)
        {
            $this->setUrl(array_shift($params));
        }

        foreach($params as $param)
        {
            switch ($param)
            {
                case 'primary':
                case 'info':
                case 'success':
                case 'warning':
                case 'danger':
                case 'link':
                case 'default':
                case 'inverse':
                    $this->type = $param;
                    break;
                case 'large':
                case 'lg':
                    $this->size = 'large';
                    break;
                case 'small':
                case 'sm':
                    $this->size = 'small';
                    break;
                case 'mini':
                case 'xs':
                    $this->size = 'x-small';
                    break;
                case 'block':
                    $this->block = true;
                    break;
                default:
                    $this->customCssClassName = trim($this->customCssClassName . ' ' . trim($param));
            }
        }        
    }
}
