<?php
namespace Toiee\HaikMarkdown\Plugin;

class PluginCounter {

    private static $instance = null;

    /**
     * Get Singleton instance
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    private $counts = array();

    public function inc($id)
    {
        if (isset($this->counts[$id]))
        {
            $this->counts[$id] += 1;
        }
        else
        {
            $this->counts[$id] = 1;
        }
    }

    public function get($id)
    {
        if (isset($this->counts[$id]))
        {
            return $this->counts[$id];
        }
        else
        {
            return 0;
        }
    }

    public function all()
    {
        return $this->counts;
    }

}
