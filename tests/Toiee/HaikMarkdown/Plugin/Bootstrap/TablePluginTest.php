<?php

use Toiee\HaikMarkdown\Plugin\Bootstrap\Table\TablePlugin;

class TablePluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $table_html = "<table>\n<thead>\n<tr>\n  <th>Header</th>\n  <th>Header Right</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n  <td>Item</td>\n  <td>Item</td>\n</tr>\n<tr>\n  <td>Item</td>\n  <td>Item</td>\n</tr>\n</tbody>\n</table>\n";
            $mock->shouldReceive('transform')->andReturn($table_html);
            return $mock;
        });
        $this->plugin = new TablePlugin($this->parser);
    }

    public function testConvertMethodExists()
    {
        $this->assertInternalType('string', $this->plugin->convert());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsExceptionWhenCallInline()
    {
        $this->plugin->inline();
    }

    public function testAddTableClassAttribute()
    {
        $result = $this->plugin->convert();
        $expected = array(
            'tag' => 'table',
            'attributes' => array(
                'class' => 'table',
            ),
        );
        $this->assertTag($expected, $result);
    }

    public function testNotAddTableClassNameDescendantTableElements()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $table_html = "<table>\n<thead>\n<tr>\n  <th>Header</th>\n  <th>Header Right</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n  <td>Item</td>\n  <td>Item</td>\n</tr>\n<tr>\n  <td>Item</td>\n  <td><table>\n <tbody>\n  <tr>\n   <td></td>\n   <td></td>\n   <td></td>\n  </tr>\n </tbody>\n</table></td>\n</tr>\n</tbody>\n</table>\n";
            $mock->shouldReceive('transform')->andReturn($table_html);
            return $mock;
        });
        $this->plugin = new TablePlugin($this->parser);
        
        $result = $this->plugin->convert();
        $expected = array(
            'tag' => 'table',
            'attributes' => array(
                'class' => 'table',
            ),
        );
        $this->assertTag($expected, $result);

        $not_expected = array(
            'tag' => 'table',
            'attributes' => array(
                'class' => 'table',
            ),
            'ancestor' => array(
                'tag' => 'table',
                'attributes' => array(
                    'class' => 'table'
                )
            )
        );
        $this->assertNotTag($not_expected, $result);
    }

    /**
     * @dataProvider paramsProvider
     */
    public function testParams($params, $expected)
    {
        $result = $this->plugin->convert($params);
        $this->assertTag($expected, $result);
    }

    public function paramsProvider()
    {
        return array(
            array(
                array('striped'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-striped'
                    )
                ),
            ),
            array(
                array('bordered'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-bordered'
                    )
                ),
            ),
            array(
                array('hover'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-hover'
                    )
                ),
            ),
            array(
                array('condensed'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-condensed'
                    )
                ),
            ),
            array(
                array('bordered', 'striped'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-bordered'
                    )
                ),
            ),
            array(
                array('bordered', 'condensed'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-bordered table-condensed'
                    )
                ),
            ),
            array(
                array('class-name'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table class-name'
                    )
                ),
            ),
            array(
                array('bordered', 'class-name'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-bordered class-name'
                    )
                ),
            ),
            array(
                array('class-name1', 'class-name2'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table class-name1 class-name2'
                    )
                ),
            ),
            array(
                array('responsive'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table'
                    ),
                    'parent' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'table-responsive'
                        ),
                    )
                ),
            ),
            array(
                array('responsive', 'bordered'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-bordered'
                    ),
                    'parent' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'table-responsive'
                        ),
                    )
                ),
            ),
            array(
                array('responsive', 'class-name'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table class-name'
                    ),
                    'parent' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'table-responsive'
                        ),
                    )
                ),
            ),
            array(
                array('responsive', 'striped', 'class-name'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-striped class-name'
                    ),
                    'parent' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'table-responsive'
                        ),
                    )
                ),
            ),

            array(
                array('type' => 'striped'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-striped'
                    )
                ),
            ),
            array(
                array('type' => 'bordered'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-bordered'
                    )
                ),
            ),
            array(
                array('type' => 'hover'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-hover'
                    )
                ),
            ),
            array(
                array('type' => 'hoge'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table'
                    )
                ),
            ),
            array(
                array('condensed' => null),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-condensed'
                    )
                ),
            ),
            array(
                array('type' => 'bordered', 'type' => 'striped'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-striped'
                    )
                ),
            ),
            array(
                array('type' => 'bordered', 'condensed' => null),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-bordered table-condensed'
                    )
                ),
            ),
            array(
                array('class' => 'class-name'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table class-name'
                    )
                ),
            ),
            array(
                array('type' => 'bordered', 'class' => 'class-name'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-bordered class-name'
                    )
                ),
            ),
            array(
                array('class' => 'class-name1', 'class' => 'class-name2'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table class-name2'
                    )
                ),
            ),
            array(
                array('class' => 'class-name1 class-name2'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table class-name1 class-name2'
                    )
                ),
            ),
            array(
                array('responsive' => 'null'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table'
                    ),
                    'parent' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'table-responsive'
                        ),
                    )
                ),
            ),
            array(
                array('responsive' => null, 'type' => 'bordered'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-bordered'
                    ),
                    'parent' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'table-responsive'
                        ),
                    )
                ),
            ),
            array(
                array('responsive' => null, 'class' => 'class-name'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table class-name'
                    ),
                    'parent' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'table-responsive'
                        ),
                    )
                ),
            ),
            array(
                array('responsive' => null, 'type' => 'striped', 'class' => 'class-name'),
                array(
                    'tag' => 'table',
                    'attributes' => array(
                        'class' => 'table table-striped class-name'
                    ),
                    'parent' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'table-responsive'
                        ),
                    )
                ),
            ),

        );
    }

    public function testKeepExistingClassName()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $table_html = "<table class=\"class-name\">\n<thead>\n<tr>\n  <th>Header</th>\n  <th>Header Right</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n  <td>Item</td>\n  <td>Item</td>\n</tr>\n<tr>\n  <td>Item</td>\n  <td>Item</td>\n</tr>\n</tbody>\n</table>\n";
            $mock->shouldReceive('transform')->andReturn($table_html);
            return $mock;
        });
        $this->plugin = new TablePlugin($this->parser);
        
        $result = $this->plugin->convert();
        $expected = array(
            'tag' => 'table',
            'attributes' => array(
                'class' => 'class-name table',
            ),
        );
        $this->assertTag($expected, $result);
    }

    
}
