<?php
namespace Toiee\HaikMarkdown\Plugin\Repositories;

use Michelf\MarkdownInterface;

abstract class AbstractPluginRepository implements PluginRepositoryInterface {

    /** @var MarkdownInterface */
    protected $parser;

    protected $repositoryPath;

    public function __construct(MarkdownInterface $parser)
    {
        $this->parser = $parser;
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
        return false;
    }

    /**
     * load Plugin by id
     * @params string $id plugin id
     * @return \Toiee\HaikMarkdown\Plugin\PluginInterface The Plugin
     * @throws InvalidArgumentException when $id was not exist
     */
    public function load($id)
    {
        if ($this->exists($id))
        {
            return $this->factory($id);
        }

        throw new \InvalidArgumentException("A plugin with id=$id was not exist");
    }

    protected function factory($id)
    {
        $class_name = $this->getClassName($id);
        return new $class_name;
    }

    /**
     * get all plugin list
     * @return array of plugin id
     */
    public function getAll()
    {
        $plugin_dir = $this->repositoryPath;
        $dirs = glob($plugin_dir);

        $plugins = array();
        foreach ($dirs as $dir)
        {
            $plugin_id = snake_case(basename($dir));
            if ($this->exists($plugin_id))
            {
                $plugins[] = $plugin_id;
            }
        }
        return $plugins;
    }

    /**
     * Get HaikMarkdown Plugin Class Name
     *
     * @param string $id Plugin ID
     * @return string class FQDN
     */
    abstract protected function getClassName($id);

}
