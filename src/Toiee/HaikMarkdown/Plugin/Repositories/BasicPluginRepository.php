<?php
namespace Toiee\HaikMarkdown\Plugin\Repositories;

class BasicPluginRepository extends AbstractPluginRepository {

    public function __construct()
    {
        $this->repositoryPath = dirname(__DIR__) . '/Basic';
        parent::__construct();
    }

    protected function getClassName($id)
    {
        $class_name = studly_case($id);
        return $class_name = 'Toiee\HaikMarkdown\Plugin\Basic\\' . $class_name . '\\' . $class_name . 'Plugin';
    }
}
