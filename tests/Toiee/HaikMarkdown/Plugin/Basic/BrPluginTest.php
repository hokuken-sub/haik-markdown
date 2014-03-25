<?php
use Toiee\HaikMarkdown\Plugin\Basic\Br\BrPlugin;

class BrPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface');
    }

    public function testInlineMethodExists()
    {
        $this->assertInternalType('string', with(new BrPlugin($this->parser))->inline());
    }
    
    public function testConvertMethodExists()
    {
        $this->assertInternalType('string', with(new BrPlugin($this->parser))->convert());
    }
    
    public function testIsInlineMethodReturnRight()
    {
        $normal = array('br' => array(), 'assert' => "<br>\n");
        $this->assertEquals($normal['assert'], with(new BrPlugin($this->parser))->inline());
    }

    public function testIsConvertMethodReturnRight()
    {
        $normal = array('br' => array(), 'assert' => '<br>');
        $this->assertEquals($normal['assert'], with(new BrPlugin($this->parser))->convert());
    }

}