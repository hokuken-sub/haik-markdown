<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Column;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Row;

class ColsPluginTest extends PHPUnit_Framework_TestCase {

    public function testConvertMethodExists()
    {
        $this->assertInternalType('string', with(new ColsPlugin)->convert());
    }

    public function testExistsViewFile()
    {
        $view_path = with(new ColsPlugin)->getViewPath();
        $this->assertTrue(!! $view_path);
    }

    /**
     * @dataProvider paramProvider
     */
    public function testParameter($cols, $assert)
    {
        $plugin = new ColsPlugin;
        $plugin->convert($cols, '');
        $this->assertAttributeEquals($assert, 'row', $plugin);
    }
    
    public function paramProvider()
    {
        return array(
            array(
                'cols'     => array(),
                'expected' => new Row(array(
                    new Column()
                ))
            ),
            array(
                'cols'     => array("3"),
                'expected' => new Row(array(
                    new Column("3")
                ))
            ),
            array(
                'cols'     => array("3", "9"),
                'expected' => new Row(array(
                    new Column("3"),
                    new Column("9")
                ))
            ),
            array(
                'cols'   => array("3", "3", "6"),
                'expected' => new Row(array(
                    new Column("3"),
                    new Column("3"),
                    new Column("6")
                ))
            ),
            array(
                'cols'   => array("3+9"),
                'expected' => new Row(array(
                    new Column("3+9")
                ))
            ),
            array(
                'cols'   => array("3.class-name"),
                'expected' => new Row(array(
                    new Column("3.class-name")
                ))
            ),
            array(
                'cols'   => array("6+6.class-name"),
                'expected' => new Row(array(
                    new Column("6+6.class-name")
                ))
            ),
        );
    }

    /**
     * @dataProvider pluginClassProvider
     */
    public function testPluginClassName($cols, $assert)
    {
        $plugin = new ColsPlugin;
        $plugin->convert($cols, '');
        $this->assertAttributeEquals($assert, 'row', $plugin);
    }
    
    public function pluginClassProvider()
    {
        $tests = array(
            'classname' => array(
                'cols'   => array('class=test-class'),
                'assert' => with(new Row(array(new Column)))->addClassAttribute('test-class'),
            ),
            'no-classname' => array(
                'cols'   => array('class='),
                'assert' => with(new Row(array(new Column)))->addClassAttribute(''),
            ),
        );
        
        return $tests;
    }

    /**
     * @dataProvider delimiterProvider
     */
    public function testDeleimiter($cols, $assert)
    {
        $plugin = new ColsPlugin;
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
        $plugin = new ColsPlugin;
        $plugin->convert(array(), $body);
        $this->assertAttributeEquals($assert, 'row', $plugin);
    }
    
    public function bodyProvider()
    {
        return array(
            array(
                'body'     => "str1\nstr2",
                'expected' => new Row(array(
                    with(new Column())->setContent("<p>str1\nstr2</p>")
                ))
            ),
            array(
                'body'     => "str1\n====\nstr2",
                'expected' => new Row(array(
                    with(new Column())->setColumnWidth(6)->setContent('<p>str1</p>'),
                    with(new Column())->setColumnWidth(6)->setContent('<p>str2</p>')
                ))
            ),
            array(
                'body'     => "\n====\n",
                'expected' => new Row(array(
                    with(new Column())->setColumnWidth(6),
                    with(new Column())->setColumnWidth(6)
                ))
            ),
        );
    }
    
    public function testHtml()
    {
        $this->markTestIncomplete();
        
/*
        $tests = array(
            'no_params' => array(
                'cols' => array(),
                'body' => 'test',
                'assert' => '<div class="haik-plugin-cols row">'.
                            '<div class="col-sm-12">'.\Parser::parse('test').'</div>'.
                            '</div>',
            ),
            'class' => array(
                'cols' => array(),
                'body' => "CLASS:hogeclass\ntest",
                'assert' => '<div class="haik-plugin-cols row">'.
                            '<div class="col-sm-12 hogeclass">'.\Parser::parse('test').'</div>'.
                            '</div>',
            ),
            'style' => array(
                'cols' => array(),
                'body' => "STYLE:background-color:#330000;color:#fff;\ntest",
                'assert' => '<div class="haik-plugin-cols row">'.
                            '<div class="col-sm-12" style="background-color:#330000;color:#fff;">'.\Parser::parse('test').'</div>'.
                            '</div>',
            ),
            'all' => array(
                'cols' => array('3+2','4.starbucks','++++', 'class=late'),
                'body' => "STYLE:background-color:#000;color:#ccc;\n".
                          "CLASS:burbon\n".
                          "col1\n".
                          "\n++++\n".
                          "col2\n".
                          "col3",
                'assert' => '<div class="haik-plugin-cols row late">'.
                            '<div class="col-sm-3 col-sm-offset-2 burbon" style="background-color:#000;color:#ccc;">'.\Parser::parse("col1").'</div>'.
                            '<div class="col-sm-4 starbucks">'.\Parser::parse("col2\ncol3").'</div>'.
                            '</div>',
            ),
            'nodelimiter' => array(
                'cols' => array('3+2','4+1.starbucks','class=tea'),
                'body' => "STYLE:background-color:#000;color:#ccc;\n".
                          "CLASS:burbon\n".
                          "col1\n".
                          "\n====\n".
                          "STYLE:background-color:#f33;color:#222;\n".
                          "CLASS:cafe\n".
                          "col2\n".
                          "col3",
                'assert' => '<div class="haik-plugin-cols row tea">'.
                            '<div class="col-sm-3 col-sm-offset-2 burbon" style="background-color:#000;color:#ccc;">'.\Parser::parse("col1").'</div>'.
                            '<div class="col-sm-4 col-sm-offset-1 starbucks cafe" style="background-color:#f33;color:#222;">'.\Parser::parse("col2\ncol3").'</div>'.
                            '</div>',
            ),
            'diffColsOverBody' => array(
                'cols' => array(6,6),
                'body' => "col1\n".
                          "\n====\n".
                          "col2\n".
                          "\n====\n".
                          "col3",
                'assert' => '<div class="haik-plugin-cols row">'.
                            '<div class="col-sm-6">'.\Parser::parse("col1").'</div>'.
                            '<div class="col-sm-6">'.\Parser::parse("col2\n\n====\ncol3").'</div>'.
                            '</div>',
            ),
            'diffColsLessBody' => array(
                'cols' => array(4,4,4),
                'body' => "col1\n".
                          "\n====\n".
                          "col2",
                'assert' => '<div class="haik-plugin-cols row">'."\n".
                            '<div class="col-sm-4">'.\Parser::parse("col1").'</div>'.
                            '<div class="col-sm-4">'.\Parser::parse("col2").'</div>'.
                            '<div class="col-sm-4">'.'</div>'.
                            '</div>',
            ),
        );

        foreach ($tests as $key => $data)
        {
            $data['assert'] = preg_replace('/\n| {2,}/', '', trim($data['assert']));
            $cmpdata = with(new ColsPlugin)->convert($data['cols'], $data['body']);
            $cmpdata = preg_replace('/\n| {2,}/', '', trim($cmpdata));
            $this->assertEquals($data['assert'], $cmpdata);
        }
*/
    }
    

    /**
     * @dataProvider overColumnSizeProvider
     */
    public function testOverColumnSize($cols, $expected)
    {
        $plugin = new ColsPlugin();
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