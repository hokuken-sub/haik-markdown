<?php
use Toiee\HaikMarkdown\Plugin\Pure\Units\UnitsPlugin;
use Toiee\HaikMarkdown\Plugin\Pure\Column;
use Toiee\HaikMarkdown\Plugin\Pure\Row;

class PureUnitsPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('');
        });
    }

    public function testConvertMethodExists()
    {
        $this->assertInternalType('string', with(new UnitsPlugin($this->parser))->convert());
    }

    /**
     * @dataProvider paramProvider
     */
    public function testParameter($cols, $assert)
    {
        $plugin = new UnitsPlugin($this->parser);
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
                    new Column("3")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'     => array("3", "9"),
                'expected' => with(new Row(array(
                    new Column("3"),
                    new Column("9")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'   => array("3", "3", "6"),
                'expected' => with(new Row(array(
                    new Column("3"),
                    new Column("3"),
                    new Column("6")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'   => array("1-3"),
                'expected' => with(new Row(array(
                    new Column("1-3")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'   => array("3.class-name"),
                'expected' => with(new Row(array(
                    new Column("3.class-name")
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'cols'   => array("1-2.class-name"),
                'expected' => with(new Row(array(
                    new Column("1-2.class-name")
                )))->prependClassAttribute('haik-plugin-units')
            ),
        );
    }

    /**
     * @dataProvider pluginClassProvider
     */
    public function testPluginClassName($cols, $assert)
    {
        $plugin = new UnitsPlugin($this->parser);
        $plugin->convert($cols, '');
        $this->assertAttributeEquals($assert, 'row', $plugin);
    }
    
    public function pluginClassProvider()
    {
        $tests = array(
            'classname' => array(
                'cols'   => array('class=test-class'),
                'assert' => with(new Row(array(new Column)))->prependClassAttribute('haik-plugin-units')->addClassAttribute('test-class'),
            ),
            'no-classname' => array(
                'cols'   => array('class='),
                'assert' => with(new Row(array(new Column)))->prependClassAttribute('haik-plugin-units')->addClassAttribute(''),
            ),
        );
        
        return $tests;
    }

    /**
     * @dataProvider delimiterProvider
     */
    public function testDeleimiter($cols, $assert)
    {
        $plugin = new UnitsPlugin($this->parser);
        $plugin->convert($cols, '');
        $this->assertAttributeSame($assert, 'delimiter', $plugin);
    }
    
    public function delimiterProvider()
    {
        $tests = array(
            'delimiter' => array(
                'cols'   => array('++++'),
                'assert' => "\n++++\n",
            ),
            'no-delimiter' => array(
                'cols'   => array(),
                'assert' => "\n====\n",
            ),
        );
        
        return $tests;
    }
    
    /**
     * @dataProvider bodyProvider
     */
    public function testParseBody($body, $assert)
    {
        $plugin = new UnitsPlugin($this->parser);
        $plugin->convert(array(), $body);
        $this->assertAttributeEquals($assert, 'row', $plugin);
    }
    
    public function bodyProvider()
    {
        return array(
            array(
                'body'     => "str1\nstr2",
                'expected' => with(new Row(array(
                    with(new Column())->setContent('')
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'body'     => "str1\n====\nstr2",
                'expected' => with(new Row(array(
                    with(new Column())->setUnitSize(1, 2)->setContent(''),
                    with(new Column())->setUnitSize(1, 2)->setContent('')
                )))->prependClassAttribute('haik-plugin-units')
            ),
            array(
                'body'     => "\n====\n",
                'expected' => with(new Row(array(
                    with(new Column())->setUnitSize(1, 2),
                    with(new Column())->setUnitSize(1, 2)
                )))->prependClassAttribute('haik-plugin-units')
            ),
        );
    }

    /**
     * @dataProvider overColumnSizeProvider
     */
    public function testOverColumnSize($cols, $expected)
    {
        $plugin = new UnitsPlugin($this->parser);
        $plugin->convert($cols);
        $this->assertAttributeEquals($expected, 'violateColumnSize', $plugin);
    }

    public function overColumnSizeProvider()
    {
        return array(
            array(
                'cols' => array('1', '1'),
                'expected' => false
            ),
            array(
                'cols' => array('12', '12'),
                'expected' => false
            ),
            array(
                'cols' => array('12', '13'),
                'expected' => true
            ),
            array(
                'cols' => array('24', '24'),
                'expected' => true
            ),
            array(
                'cols' => array('1-2', '1-2'),
                'expected' => false
            ),
            array(
                'cols' => array('2-3', '2-3'),
                'expected' => true
            ),
        );
    }

}
