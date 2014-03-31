<?php

use Toiee\HaikMarkdown\Plugin\Pure\Cols\ColsPlugin;
use Toiee\HaikMarkdown\Plugin\Pure\Row;
use Toiee\HaikMarkdown\Plugin\Pure\Column;

class PureColsPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('');
        });
        $this->plugin = new ColsPlugin($this->parser);
    }

    public function testExistsConvertMethod()
    {
        $this->assertInternalType('string', $this->plugin->convert());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testThrowsExceptionWhenCallInlineMethod()
    {
        $this->plugin->inline();
    }

    /**
     * @dataProvider paramProvider
     */
    public function testParameter($cols, $assert)
    {
        $plugin = new ColsPlugin($this->parser);
        $plugin->convert($cols, '');
        $this->assertAttributeEquals($assert, 'row', $plugin);
    }
    
    public function paramProvider()
    {
        return array(
            array(
                'cols'     => array(),
                'expected' => with(new Row(array(
                    new Column()
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'     => array("3"),
                'expected' => with(new Row(array(
                    new Column("6")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'     => array("3", "9"),
                'expected' => with(new Row(array(
                    new Column("6"),
                    new Column("18")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'   => array("3", "3", "6"),
                'expected' => with(new Row(array(
                    new Column("6"),
                    new Column("6"),
                    new Column("12")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'   => array("3+9"),
                'expected' => with(new Row(array(
                    new Column("18"),
                    new Column("6")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'   => array("3.class-name"),
                'expected' => with(new Row(array(
                    new Column("6.class-name")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'   => array("6+6.class-name"),
                'expected' => with(new Row(array(
                    new Column("12"),
                    new Column("12.class-name")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'   => array("2-3"),
                'expected' => with(new Row(array(
                    new Column()
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'   => array("1-4.class-name"),
                'expected' => with(new Row(array(
                    new Column()
                )))->prependClassAttribute('haik-plugin-units')
            ),
        );
    }

}
