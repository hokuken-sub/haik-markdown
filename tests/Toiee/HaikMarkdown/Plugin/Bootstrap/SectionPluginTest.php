<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Section\SectionPlugin;
use Michelf\MarkdownExtra;
use Toiee\HaikMarkdown\HaikMarkdown;

class SectionPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('<p>test</p>');
            return $mock;
        });
        $this->plugin = new SectionPlugin($this->parser);
    }

    public function testCalledPlugin()
    {
        $plugin =  new SectionPlugin(new HaikMarkdown);

        $result = $plugin->convert();
        $this->assertTag(array(
            'tag' => 'style',
        ),
        $result);
var_dump($result);
        $result = $plugin->convert();
        $this->assertNotTag(array(
            'tag' => 'style',
        ),
        $result);
    }



    public function testConvertMethodExists()
    {
        $result = $this->plugin->convert();
        $this->assertInternalType('string', $result);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsExceptionWhenCallInline()
    {
        $this->plugin->inline();
    }


    /**
     * @dataProvider paramProvider
     */
    public function testParamHtml($params, $expected)
    {
        $this->plugin = new SectionPlugin($this->parser);
        $result = $this->plugin->convert($params, 'test');
        $this->assertTag($expected, $result);
    }

    public function paramProvider()
    {
        return array(
            'no_params' => array(
                'params' => array(),
                'expected' => array(
                    'ancestor' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'haik-plugin-section'
                        ),
                    ),
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                    ),
                    'child' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'container',
                        )
                    ),
                ),
            ),
            'align_center' => array(
                'params' => array('center'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron text-center',
                    ),
                ),
            ),
            'align_left' => array(
                'params' => array('left'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron text-left',
                    ),
                ),
            ),
            'align_right' => array(
                'params' => array('right'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron text-right',
                    ),
                ),
            ),
            'nojumbotron' => array(
                'params' => array('center', 'nojumbotron'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'text-center',
                    ),
                ),
            ),
            'valign_top' => array(
                'params' => array('top'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                    ),
                    'child' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'container',
                            'style' => 'vertical-align:top',
                        )
                    ),
                ),
            ),
            'valign_middle' => array(
                'params' => array('middle'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                    ),
                    'child' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'container',
                            'style' => 'vertical-align:middle',
                        )
                    ),
                ),
            ),
            'valign_bottom' => array(
                'params' => array('bottom'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                    ),
                    'child' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'container',
                            'style' => 'vertical-align:bottom',
                        )
                    ),
                ),
            ),
            'height_number' => array(
                'params' => array('height=500'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'min-height:500px'
                    ),
                ),
            ),
            'height_px' => array(
                'params' => array('height=400px'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'min-height:400px'
                    ),
                ),
            ),
            'all_param' => array(
                'params' => array('center','middle','height=300px'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron text-center',
                        'style' => 'min-height:300px'
                    ),
                    'child' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'container',
                            'style' => 'vertical-align:middle',
                        )
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider configProvider
     */
    public function testConfigHtml($body, $expected)
    {
        $this->plugin = new SectionPlugin($this->parser);
        $result = $this->plugin->convert(array(), $body);
        $this->assertTag($expected, $result);
    }

    public function configProvider()
    {
        $delim = "****";
        return array(
            'no_config' => array(
                'body' => "\ntest\n\n".$delim."\n",
                'expected' => array(
                    'ancestor' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'haik-plugin-section'
                        ),
                    ),
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                    ),
                    'child' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'container',
                        )
                    ),
                ),
            ),
            'bg_color' => array(
                'body' => "\ntest\n\n".$delim."\n"."BG_COLOR: #ddd\n\n",
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'background-color:#ddd',
                    ),
                ),
            ),
            'bg_image' => array(
                'body' => "\ntest\n\n".$delim."\n"."BG_IMAGE: image/test.png\n\n",
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'background-image:url(image/test.png)',
                    ),
                ),
            ),
            'color' => array(
                'body' => "\ntest\n\n".$delim."\n"."COLOR: #333\n\n",
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'color:#333',
                    ),
                ),
            ),
            'allconfig' => array(
                'body' => "\ntest\n\n".$delim."\n"."BG_COLOR: #eee\n\n"."BG_IMAGE: image/img.png\n\n"."COLOR: #444\n\n",
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'color:#444;background-image:url(image/img.png);background-color:#eee',
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider bodyProvider
     */
    public function testBodyHtml($body, $expected)
    {
        $this->plugin = new SectionPlugin($this->parser);
        $result = $this->plugin->convert(array(), $body);
        $this->assertTag($expected, $result);
    }

    public function bodyProvider()
    {
        $config_delim = "\n****\n";
        $col_delim = "\n====\n";
        return array(
            'col' => array(
                'body' => "\ntest\n".$col_delim."\ntest\n".$config_delim."\n",
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'container',
                    ),
                    'child' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'row'
                        ),
                    ),
                    'descendant' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'row',
                        ),
                        'children' => array(
                            'only' => array(
                                'tag' => 'div',
                                'attributes' => array(
                                    'class' => 'col-sm-6',
                                ),
                            ),
                            'count' => 2,
                        ),
                    ),
                ),
            ),
        );
    }
}
