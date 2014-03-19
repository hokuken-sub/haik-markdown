<?php
namespace Toiee\HaikMarkdown\Plugin\Repositories;

class BootstrapPluginRepository extends AbstractPluginRepository {

    public function __construct()
    {
        $this->repositoryPath = dirname(__DIR__) . '/Bootstrap';
    }

    protected function getClassName($id)
    {
        $class_name = studly_case($id);
        return $class_name = 'Toiee\HaikMarkdown\Plugin\Bootstrap\\' . $class_name . '\\' . $class_name . 'Plugin';
    }

}
