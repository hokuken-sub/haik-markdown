<?php
use Hokuken\HaikMarkdown\Plugin\Basic\Br\BrPlugin;

class BrPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface');
        $this->plugin = new BrPlugin($this->parser);
    }

    public function testInlineMethodExists()
    {
        $this->assertInternalType('string', $this->plugin->inline());
    }
    
    public function testConvertMethodExists()
    {
        $this->assertInternalType('string', $this->plugin->convert());
    }
    
    public function testIsInlineMethodReturnRight()
    {
        $normal = array('br' => array(), 'assert' => "<br>\n");
        $this->assertEquals($normal['assert'], $this->plugin->inline());
    }

    public function testIsConvertMethodReturnRight()
    {
        $normal = array('br' => array(), 'assert' => '<br>');
        $this->assertEquals($normal['assert'], $this->plugin->convert());
    }

}
