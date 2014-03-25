<?php
namespace Toiee\HaikMarkdown\Plugin\Repositories;

use Michelf\MarkdownInterface;

class BasicPluginRepository extends AbstractPluginRepository {

    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);
        $this->repositoryPath = dirname(__DIR__) . '/Basic';
    }

    protected function getClassName($id)
    {
        $class_name = studly_case($id);
        return $class_name = 'Toiee\HaikMarkdown\Plugin\Basic\\' . $class_name . '\\' . $class_name . 'Plugin';
    }
}
