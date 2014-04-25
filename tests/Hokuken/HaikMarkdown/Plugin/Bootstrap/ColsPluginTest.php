<?php
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Column;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Row;

class ColsPluginTest extends PHPUnit_Framework_TestCase {

    public function with($arg)
    {
        return $arg;
    }

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('');
        });
    }
    public function testConvertMethodExists()
    {
        $plugin = new ColsPlugin($this->parser);
        $result = $plugin->convert();
        $this->assertInternalType('string', $result);
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
                'expected' => $this->with(new Row(array(
                    new Column()
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'     => array("3"),
                'expected' => $this->with(new Row(array(
                    new Column("3")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'     => array("3", "9"),
                'expected' => $this->with(new Row(array(
                    new Column("3"),
                    new Column("9")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'   => array("3", "3", "6"),
                'expected' => $this->with(new Row(array(
                    new Column("3"),
                    new Column("3"),
                    new Column("6")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'   => array("3+9"),
                'expected' => $this->with(new Row(array(
                    new Column("3+9")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'   => array("3.class-name"),
                'expected' => $this->with(new Row(array(
                    new Column("3.class-name")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'cols'   => array("6+6.class-name"),
                'expected' => $this->with(new Row(array(
                    new Column("6+6.class-name")
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            // hash params
            [
                ['columns' => '6, 6'],
                $this->with(new Row(array(
                    new Column("6"),
                    new Column("6")
                )))->prependClassAttribute('haik-plugin-cols')
            ],
            [
                ['cols' => '3, 9'],
                $this->with(new Row(array(
                    new Column("3"),
                    new Column("9")
                )))->prependClassAttribute('haik-plugin-cols')
            ],
            [
                ['cols' => '6.class-name1, 6.class-name2'],
                $this->with(new Row(array(
                    new Column("6.class-name1"),
                    new Column("6.class-name2")
                )))->prependClassAttribute('haik-plugin-cols')
            ],
            [
                ['cols' => '3+3, 3+3'],
                $this->with(new Row(array(
                    new Column("3+3"),
                    new Column("3+3")
                )))->prependClassAttribute('haik-plugin-cols')
            ],
            [
                ['cols' => [
                    ['span' => 4],
                    ['span' => 4, 'class' => 'class-name'],
                    ['span' => 4, 'style' => 'text-align:center']
                ]],
                $this->with(new Row(array(
                    new Column("4"),
                    new Column("4.class-name"),
                    $this->with(new Column("4"))->addStyleAttribute('text-align:center')
                )))->prependClassAttribute('haik-plugin-cols')
            ],
            [
                ['cols' => [
                    ['span' => '6.class-name'],
                ]],
                $this->with(new Row(array(
                    new Column("6.class-name")
                )))->prependClassAttribute('haik-plugin-cols')
            ],            
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
            'classname #1' => array(
                'cols'   => array('class' => 'test-class'),
                'assert' => $this->with(new Row(array(new Column)))->prependClassAttribute('haik-plugin-cols')->addClassAttribute('test-class'),
            ),
            'classname #2' => array(
                'cols'   => array(array('class' => 'test-class')),
                'assert' => $this->with(new Row(array(new Column)))->prependClassAttribute('haik-plugin-cols')->addClassAttribute('test-class'),
            ),
            'no-classname' => array(
                'cols'   => array('class' => null),
                'assert' => $this->with(new Row(array(new Column)))->prependClassAttribute('haik-plugin-cols')->addClassAttribute(''),
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
            'no-delimiter' => array(
                'cols'   => array(),
                'assert' => "\n====\n",
            ),
            'delimiter:' => array(
                'cols'   => [['delimiter' => '++++']],
                'assert' => "\n++++\n",
            ),
            'delim:' => array(
                'cols'   => [['delim' => '++++']],
                'assert' => "\n++++\n",
            ),
            'separator:' => array(
                'cols'   => [['separator' => '++++']],
                'assert' => "\n++++\n",
            ),
            'sep:' => array(
                'cols'   => [['sep' => '++++']],
                'assert' => "\n++++\n",
            ),
            // hash param
            '#delimiter' => [
                ['delimiter' => '++++'],
                "\n++++\n",
            ],
            '#delimiter:null' => [
                ['delimiter' => null],
                "\n====\n",
            ],
            '#delimiter:empty' => [
                ['delimiter' => ''],
                "\n====\n",
            ],
            '#delimiter:space_only' => [
                ['delimiter' => '   '],
                "\n====\n",
            ],
            '#delim' => [
                ['delim' => '++++'],
                "\n++++\n",
            ],
            '#separator' => [
                ['separator' => '++++'],
                "\n++++\n",
            ],
            '#sep' => [
                ['sep' => '++++'],
                "\n++++\n",
            ],
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
                'expected' => $this->with(new Row(array(
                    $this->with(new Column())->setContent('')
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'body'     => "str1\n====\nstr2",
                'expected' => $this->with(new Row(array(
                    $this->with(new Column())->setColumnWidth(6)->setContent(''),
                    $this->with(new Column())->setColumnWidth(6)->setContent('')
                )))->prependClassAttribute('haik-plugin-cols')
            ),
            array(
                'body'     => "\n====\n",
                'expected' => $this->with(new Row(array(
                    $this->with(new Column())->setColumnWidth(6),
                    $this->with(new Column())->setColumnWidth(6)
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

    public function specialAttributeProvider()
    {
        return [
            [
                ['id' => 'cols_id'],
                [
                    'tag' => 'div',
                    'attributes' => [
                        'id' => 'cols_id',
                        'class' => 'haik-plugin-cols'
                    ]
                ]
            ],
            [
                ['class' => 'cols-class'],
                [
                    'tag' => 'div',
                    'attributes' => [
                        'class' => 'haik-plugin-cols cols-class'
                    ],
                ]
            ],
            [
                ['id' => 'cols_id', 'class' => 'cols-class'],
                [
                    'tag' => 'div',
                    'attributes' => [
                        'id' => 'cols_id',
                        'class' => 'haik-plugin-cols cols-class'
                    ],
                ]
            ],
        ];
    }

    /**
     * @dataProvider specialAttributeProvider
     */
    public function testSpecialAttribute($attrs, $expected)
    {
        $plugin = new ColsPlugin($this->parser);
        $body = "\n\n";
        isset($attrs['id']) && $plugin->setSpecialIdAttribute($attrs['id']);
        isset($attrs['class']) && $plugin->setSpecialClassAttribute($attrs['class']);
        $result = $plugin->convert([], $body);
        $this->assertTag($expected, $result);
    }


}
