<?php
namespace Toiee\HaikMarkdown\Plugin\Repositories;

use Toiee\HaikMarkdown\Plugin\PluginCounter;

class PluginRepository implements PluginRepositoryInterface {

    protected $repositories;

    public function __construct()
    {
        $this->repositories = array();
    }

    /**
     * Register PluginRepositoryInterface
     *
     * @param PluginRepositoryInterface $repository
     * @return PluginRepository $this for method chain
     */
    public function register(PluginRepositoryInterface $repository)
    {
        array_unshift($this->repositories, $repository);

        return $this;
    }

    /**
     * plugin $id is exists?
     * @params string $id plugin id
     * @return boolean
     */
    public function exists($id)
    {
        foreach ($this->repositories as $repository)
        {
            if ($repository->exists($id)) return true;
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
        foreach ($this->repositories as $repository)
        {
            if ($repository->exists($id))
            {
                $this->incrementPluginCount($id);
                return $repository->load($id);
            }
        }

        throw new \InvalidArgumentException("A plugin with id=$id was not exist");
    }

    /**
     * get all plugin list
     * @return array of plugin id
     */
    public function getAll()
    {
        $plugins = array();
        foreach ($this->repositories as $repository)
        {
            $plugins += $repository->getAll();
        }

        $plugins = array_unique($plugins);
        sort($plugins, SORT_NATURAL | SORT_FLAG_CASE);

        return $plugins;
    }

    protected function incrementPluginCount($id)
    {
        PluginCounter::getInstance()->inc($id);
    }

}
