<?php
namespace Hokuken\HaikMarkdown\Plugin\FlatUI;

use Michelf\MarkdownInterface;
use Hokuken\HaikMarkdown\Plugin\Repositories\AbstractPluginRepository;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\PluginRepository as BootstrapPluginRepository;

class PluginRepository extends AbstractPluginRepository {

    protected $bootstrap_repository;

    public function __construct(MarkdownInterface $parser)
    {
        parent::__construct($parser);
        $this->bootstrap_repository = new BootstrapPluginRepository($parser);
        $this->repositoryPath = __DIR__;
    }

    /**
     * plugin $id is exists?
     * @params string $id plugin id
     * @return boolean
     */
    public function exists($id)
    {
        if (class_exists($this->getClassName($id), true))
        {
            return true;
        }

        if ($this->bootstrap_repository->exists($id))
        {
            return true;
        }

        return false;
    }

    /**
     * load Plugin by id
     * @params string $id plugin id
     * @return \Hokuken\HaikMarkdown\Plugin\PluginInterface The Plugin
     * @throws InvalidArgumentException when $id was not exist
     */
    public function load($id)
    {
        if (class_exists($this->getClassName($id), true))
        {
            $class_name = $this->getClassName($id);
            return new $class_name($this->parser);
        }

        return $this->bootstrap_repository->load($id);
    }

    public function getAll()
    {
        $plugins = array_merge($this->bootstrap_repository->getAll(), parent::getAll());
        return $plugins;
    }

    protected function getClassName($id)
    {
        $text = ucwords(str_replace(array('-', '_'), ' ', $id));
        $class_name = str_replace(' ', '', $text);
        return $class_name = 'Hokuken\HaikMarkdown\Plugin\FlatUI\\' . $class_name . '\\' . $class_name . 'Plugin';
    }

}
