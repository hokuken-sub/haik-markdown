<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap;

use Toiee\HaikMarkdown\Plugin\Plugin as BasePlugin;

abstract class Plugin extends BasePlugin {

    protected $view;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * render view file
     *
     * @params array $data
     * @return string converted HTML string
     */
    public function renderView($data = array())
    {
        return $this->render($this->view, $data);
    }

    /**
     * render specified view file
     *
     * @param string $view name of viewfile e.g.) "plugin_name.template"
     * @param array $data
     * @return string converted HTML string
     */
    public function render($view, $data = array())
    {
        return '';
    }

}
