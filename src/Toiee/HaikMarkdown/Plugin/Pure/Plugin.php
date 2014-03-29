<?php
namespace Toiee\HaikMarkdown\Plugin\Pure;

use Toiee\HaikMarkdown\Plugin\Bootstrap\Plugin as BootstrapPlugin;

abstract class Plugin extends BootstrapPlugin {

    protected $view;

    protected function getViewPath()
    {
        $view_path = dirname(__DIR__) . '/../../../../views/pure/' . str_replace('.', '/', $this->view) . '.php';
        if ( ! file_exists($view_path))
        {
            return false;
        }
        return $view_path;
    }

}
