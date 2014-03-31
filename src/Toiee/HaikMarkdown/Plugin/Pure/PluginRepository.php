<?php
namespace Toiee\HaikMarkdown\Plugin\Pure;

use Michelf\MarkdownInterface;
use Toiee\HaikMarkdown\Plugin\Repositories\AbstractPluginRepository;

class PluginRepository extends AbstractPluginRepository {

    /** @var boolean loadable compatible plugins */
    protected $compatibleWithBootstrap;

    /** @var array list of compatible plugins */
    protected $compatiblePlugins = array(
        'cols'
    );

    public function __construct(MarkdownInterface $parser, $compatible_with_bootstrap = false)
    {
        parent::__construct($parser);
        $this->repositoryPath = __DIR__;
        $this->compatibleWithBootstrap = $compatible_with_bootstrap;
    }

    /**
     * Determine specified plugin exists.
     *
     * @see Toiee\HaikMarkdown\Plugin\Repositories\AbstractPluginRepository::exists
     */
    public function exists($id)
    {
        if ( ! $this->isAvailable($id))
        {
            return false;
        }
        return parent::exists($id);
    }

    /**
     * Determine specified plugin is available.
     * When compatiblePluginWithBootstrap is false, compatible plugins are disabled.
     *
     * @param string $id plugin ID
     * @return boolean specified plugin is available
     */
    protected function isAvailable($id)
    {
        if ( ! $this->compatibleWithBootstrap && in_array($id, $this->compatiblePlugins))
        {
            return false;
        }
        return true;
    }

    protected function getClassName($id)
    {
        $class_name = studly_case($id);
        return $class_name = 'Toiee\HaikMarkdown\Plugin\Pure\\' . $class_name . '\\' . $class_name . 'Plugin';
    }

    public function getAll()
    {
        $plugins = parent::getAll();
        $repository = $this;
        $plugins = array_filter($plugins, function($plugin) use ($repository)
        {
            if ($repository->isAvailable($plugin))
            {
                return $plugin;
            }
            return null;
        });
        return $plugins;
    }

}
