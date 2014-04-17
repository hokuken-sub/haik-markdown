<?php

use Toiee\HaikMarkdown\Plugin\Plugin;
use Toiee\HaikMarkdown\Plugin\PluginCounter;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Alert\AlertPlugin;

class PluginTest extends PHPUnit_Framework_TestCase {

    public function testId()
    {
        $this->markTestIncomplete('Plugins are not moved.');
    }
    
    public function testCheckHash()
    {
        $parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            return $mock;
        });
 
        $plugin = new AlertPlugin($parser);
        
        $data = array('foo','bar');
        $this->assertFalse($plugin->isHash($data));

        $data = array('class'=>'test', '1');
        $this->assertTrue($plugin->isHash($data));

        $data = array(1, 'class'=>'test');
        $this->assertTrue($plugin->isHash($data));

    }
}
