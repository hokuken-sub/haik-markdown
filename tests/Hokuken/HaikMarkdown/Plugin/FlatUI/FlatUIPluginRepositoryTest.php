<?php
use Hokuken\HaikMarkdown\Plugin\FlatUI\PluginRepository;

class FlatUIPluginRepositoryTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface');
    }

    public function testLoadBootstrapPlugin()
    {
        $repository = new PluginRepository($this->parser, true);
        $plugin = $repository->load('cols');
        $this->assertInstanceOf('Hokuken\HaikMarkdown\Plugin\PluginInterface', $plugin);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionWhenLoadNonePlugin()
    {
        $repository = new PluginRepository($this->parser, false);
        $plugin = $repository->load('fall');
    }

    public function testContainsCompatiblePluginWhenGetAll()
    {
        $repository = new PluginRepository($this->parser, false);
        $plugin = $repository->getAll();
        $result = in_array('cols', $plugin);
        $this->assertTrue($result);
    }

}
