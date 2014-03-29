<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap;

use Toiee\HaikMarkdown\Plugin\Plugin as BasePlugin;

abstract class Plugin extends BasePlugin {

    protected $view;

    /**
     * render view file
     *
     * @params array $data
     * @return string converted HTML string
     */
    protected function renderView($data = array())
    {
        return $this->render($this->getViewPath(), $data);
    }


    protected function getViewPath()
    {
        $view_path = dirname(__DIR__) . '/../../../../views/bootstrap/' . str_replace('.', '/', $this->view) . '.php';
        if ( ! file_exists($view_path))
        {
            return false;
        }
        return $view_path;
    }

    /**
     * render specified view file
     *
     * @param string $view name of viewfile e.g.) "plugin_name.template"
     * @param array $data
     * @return string converted HTML string
     */
    protected function render($view_path, $data = array())
    {
        if ($view_path !== false)
        {
            extract($data);
            $self = $this;
            ob_start();
            include $view_path;
            return ob_get_clean();
        }
        return '';
    }

}
