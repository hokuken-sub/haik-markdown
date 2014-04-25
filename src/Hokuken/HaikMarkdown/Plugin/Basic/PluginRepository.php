<?php
namespace Hokuken\HaikMarkdown\Plugin\Basic;

use Michelf\MarkdownInterface;
use Hokuken\HaikMarkdown\Plugin\Repositories\AbstractPluginRepository;

class PluginRepository extends AbstractPluginRepository {

    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);
        $this->repositoryPath = dirname(__DIR__) . '/Basic';
    }

    protected function getClassName($id)
    {
        $text = ucwords(str_replace(array('-', '_'), ' ', $id));
        $class_name = str_replace(' ', '', $text);
        return $class_name = 'Hokuken\HaikMarkdown\Plugin\Basic\\' . $class_name . '\\' . $class_name . 'Plugin';
    }
}
