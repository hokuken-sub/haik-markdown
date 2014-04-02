<?php
namespace Toiee\HaikMarkdown\Plugin\Bootstrap;

use Michelf\MarkdownInterface;
use Toiee\HaikMarkdown\Plugin\Repositories\AbstractPluginRepository;

class PluginRepository extends AbstractPluginRepository {

    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);
        $this->repositoryPath = __DIR__;
    }

    protected function getClassName($id)
    {
        $class_name = studly_case($id);
        return $class_name = 'Toiee\HaikMarkdown\Plugin\Bootstrap\\' . $class_name . '\\' . $class_name . 'Plugin';
    }

}
