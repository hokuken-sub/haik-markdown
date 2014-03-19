<?php
namespace Toiee\HaikMarkdown\Plugin\Repositories;

use Toiee\HaikMarkdown\Plugin\PluginCounter;

class PluginRepository implements PluginRepositoryInterface {

    private static $instance = null;

    protected $repositories;

    private function __construct()
    {
        $this->initialize();
    }

    /**
     * Clean plugin repositories.
     *
     * @return PluginRepository $this for method chain
     */
    public function clean()
    {
        $this->repositories = array();

        return $this;
    }

    /**
     * Initialize this instance.
     * Clear PluginRepositoryInterface array and register Basic/Bootstrap plugin repositories.
     *
     * @return PluginRepository $this for method chain
     */
    public function initialize()
    {
        $this->clean();

        $this->register(
            new BasicPluginRepository
        )->register(
            new BootstrapPluginRepository
        );

        return $this;
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
     * Returns the singleton instance
     *
     * @return PluginRepository
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
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
