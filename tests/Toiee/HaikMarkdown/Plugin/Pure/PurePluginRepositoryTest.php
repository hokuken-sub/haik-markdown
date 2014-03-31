<?php

use Toiee\HaikMarkdown\Plugin\Pure\PluginRepository;

class PurePluginRepositoryTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface');
    }

    public function testLoadCompatiblePluginInBootstrapCompatibleMode()
    {
        $repository = new PluginRepository($this->parser, true);
        $plugin = $repository->load('cols');
        $this->assertInstanceOf('Toiee\HaikMarkdown\Plugin\PluginInterface', $plugin);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionWhenLoadCompatiblePluginInNoCompatibleMode()
    {
        $repository = new PluginRepository($this->parser, false);
        $plugin = $repository->load('cols');
    }

    public function testContainsCompatiblePluginWhenGetAllPluginsInBootstrapCompatibleMode()
    {
        $repository = new PluginRepository($this->parser, true);
        $plugin = $repository->getAll();
        $result = in_array('cols', $plugin);
        $this->assertTrue($result);
    }

    public function testExcludeCompatiblePluginWhenGetAllPluginsInNoCompatibleMode()
    {
        $repository = new PluginRepository($this->parser, false);
        $plugin = $repository->getAll();
        $result = in_array('cols', $plugin);
        $this->assertFalse($result);
    }
}
