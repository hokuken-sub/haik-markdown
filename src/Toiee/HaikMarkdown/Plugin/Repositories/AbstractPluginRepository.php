<?php
namespace Toiee\HaikMarkdown\Plugin\Repositories;

abstract class AbstractPluginRepository implements PluginRepositoryInterface {

    protected $repositoryPath;

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
            $class_name = $this->getClassName($id);
            return new $class_name;
        }
    }

    /**
     * get all plugin list
     * @return array of plugin id
     */
    public function getAll()
    {
        $plugin_dir = $this->repositoryPath;
        $dirs = glob($plugin_dir);
        
        $plugins = array_map(function($dir) use ($this as $repository)
        {
            $plugin_id = snake_case(basename($dir));
            if ($repository->exists($id))
            {
                return $id;
            }
            return null;
        }, $dirs);
    }

    /**
     * Get HaikMarkdown Plugin Class Name
     *
     * @param string $id Plugin ID
     * @return string class FQDN
     */
    abstract protected function getClassName($id);

}
