<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Column;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Row;

class ColsPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('');
        });
    }
    public function testConvertMethodExists()
    {
        $this->assertInternalType('string', with(new ColsPlugin($this->parser))->convert());
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
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'     => array("3"),
                'expected' => with(new Row(array(
                    new Column("3")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'     => array("3", "9"),
                'expected' => with(new Row(array(
                    new Column("3"),
                    new Column("9")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'   => array("3", "3", "6"),
                'expected' => with(new Row(array(
                    new Column("3"),
                    new Column("3"),
                    new Column("6")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'   => array("3+9"),
                'expected' => with(new Row(array(
                    new Column("3+9")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'   => array("3.class-name"),
                'expected' => with(new Row(array(
                    new Column("3.class-name")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'   => array("6+6.class-name"),
                'expected' => with(new Row(array(
                    new Column("6+6.class-name")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
        );
    }

    /**
     * @dataProvider pluginClassProvider
     */
    public function testPluginClassName($cols, $assert)
    {
        $plugin = new ColsPlugin($this->parser);
        $plugin->convert($cols, '');
        $this->assertAttributeEquals($assert, 'row', $plugin);
    }
    
    public function pluginClassProvider()
    {
        $tests = array(
            'classname' => array(
                'cols'   => array('class=test-class'),
                'assert' => with(new Row(array(new Column)))->prependClassAttribute('haik-plugin-cols')->addClassAttribute('test-class'),
            ),
            'no-classname' => array(
                'cols'   => array('class='),
                'assert' => with(new Row(array(new Column)))->prependClassAttribute('haik-plugin-cols')->addClassAttribute(''),
            ),
        );
        
        return $tests;
    }

    /**
     * @dataProvider delimiterProvider
     */
    public function testDeleimiter($cols, $assert)
    {
        $plugin = new ColsPlugin($this->parser);
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
        $plugin = new ColsPlugin($this->parser);
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
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'body'     => "str1\n====\nstr2",
                'expected' => with(new Row(array(
                    with(new Column())->setColumnWidth(6)->setContent(''),
                    with(new Column())->setColumnWidth(6)->setContent('')
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'body'     => "\n====\n",
                'expected' => with(new Row(array(
                    with(new Column())->setColumnWidth(6),
                    with(new Column())->setColumnWidth(6)
                )))->prependClassAttribute('haik-plugin-cols')
            ),
        );
    }

    /**
     * @dataProvider overColumnSizeProvider
     */
    public function testOverColumnSize($cols, $expected)
    {
        $plugin = new ColsPlugin($this->parser);
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
                'cols' => array('6', '6'),
                'expected' => false
            ),
            array(
                'cols' => array('6', '7'),
                'expected' => true
            ),
            array(
                'cols' => array('12', '12'),
                'expected' => true
            ),
            array(
                'cols' => array('3+3', '3+3'),
                'expected' => false
            ),
            array(
                'cols' => array('3+4', '3+3'),
                'expected' => true
            ),
            array(
                'cols' => array('1+1', '1+1'),
                'expected' => false
            ),
            array(
                'cols' => array('7+11', '9+11'),
                'expected' => true
            ),
        );
    }

}