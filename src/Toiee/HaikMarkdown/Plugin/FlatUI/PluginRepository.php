<?php
namespace Toiee\HaikMarkdown\Plugin\FlatUI;

use Michelf\MarkdownInterface;
use Toiee\HaikMarkdown\Plugin\Repositories\AbstractPluginRepository;

class PluginRepository extends AbstractPluginRepository {

    /** @var boolean loadable compatible plugins */
    protected $compatibleWithBootstrap;

    public function __construct(MarkdownInterface $parser, $compatible_with_bootstrap = false)
    {
        parent::__construct($parser);
        $this->repositoryPath = __DIR__;
        $this->compatibleWithBootstrap = $compatible_with_bootstrap;
    }

    protected function getClassName($id)
    {
        $class_name = studly_case($id);
        return $class_name = 'Toiee\HaikMarkdown\Plugin\FlatUI\\' . $class_name . '\\' . $class_name . 'Plugin';
    }

}
