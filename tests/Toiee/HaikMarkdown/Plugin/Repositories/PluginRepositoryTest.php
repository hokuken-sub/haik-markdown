<?php

use Toiee\HaikMarkdown\Plugin\Repositories\PluginRepository;
use Toiee\HaikMarkdown\Plugin\PluginCounter;

class PluginRepositoryTest extends PHPUnit_Framework_TestCase {

    public function testCountUpWhenSuccessfullyLoad()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface');
        $repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->andReturn($plugin_mock);
        });

        $id = 'plugin';

        $count_before_load = PluginCounter::getInstance()->get($id);
        $plugin = PluginRepository::getInstance()->clean()->register($repository)->load($id);
        $count_after_load = PluginCounter::getInstance()->get($id);

        $this->assertEquals($count_before_load + 1, $count_after_load);
    }

    public function testNotCountUpWhenFailureLoad()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface');
        $repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(false);
        });

        $id = 'plugin.not.found';

        $count_before_load = PluginCounter::getInstance()->get($id);
        try {
            $plugin = PluginRepository::getInstance()->clean()->register($repository)->load($id);
        } catch (\Exception $e) {}
        $count_after_load = PluginCounter::getInstance()->get($id);

        $this->assertEquals($count_before_load, $count_after_load);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionWhenPluginNotFound()
    {
        $repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock)
        {
            $mock->shouldReceive('exists')->andReturn(false);
        });
        $id = 'plugin.not.found';

        $plugin = PluginRepository::getInstance()->clean()->register($repository)->load($id);
    }

}
