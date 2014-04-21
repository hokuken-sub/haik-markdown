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

        if (Toiee\HaikMarkdown\Plugin\PluginCounter::getInstance()->get('Toiee\HaikMarkdown\Plugin\Bootstrap\Section\SectionPlugin') === 0)
        {
            $result = $plugin->convert();
            $this->assertTag(array(
                'tag' => 'style',
            ),
            $result);
        }
        else
        {
            $result = $plugin->convert();
            $this->assertNotTag(array(
                'tag' => 'style',
            ),
            $result);
        }

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
                'params' => array(array('height'=>'500')),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'min-height:500px'
                    ),
                ),
            ),
            'height_px' => array(
                'params' => array(array('height'=>'400px')),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'min-height:400px'
                    ),
                ),
            ),
            'classname' => array(
                'params' => array(array('class'=>'testclass')),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron testclass',
                    ),
                ),
            ),
            'bg-color' => array(
                'params' => array(array('bg-color'=>'#ddd')),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'background-color:#ddd',
                    ),
                ),
            ),
            'bg-image' => array(
                'params' => array(array('bg-image'=>'image/test.png')),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'background-image:url(image/test.png)',
                    ),
                ),
            ),
            'color' => array(
                'params' => array(array('color'=>'#333')),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'color:#333',
                    ),
                ),
            ),
            'all_param' => array(
                'params' => array('center','middle',array('height'=>'300px'),array('color'=>'#555'),array('bg-image'=>'image/hoge.png'),array('bg-color'=>'#ddd')),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron text-center',
                        'style' => 'color:#555;background-image:url(image/hoge.png);background-color:#ddd;min-height:300px'
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

            'hash_align_center' => array(
                'params' => array('align' => 'center'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron text-center',
                    ),
                ),
            ),
            'hash_align_left' => array(
                'params' => array('align' => 'left'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron text-left',
                    ),
                ),
            ),
            'hash_align_right' => array(
                'params' => array('align' => 'right'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron text-right',
                    ),
                ),
            ),
            'hash_nojumbotron' => array(
                'params' => array('align' => 'center', 'nojumbotron'=> null),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'text-center',
                    ),
                ),
            ),
            'hash_valign_top' => array(
                'params' => array('valign' => 'top'),
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
            'hash_valign_middle' => array(
                'params' => array('valign' => 'middle'),
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
            'hash_valign_bottom' => array(
                'params' => array('valign' => 'bottom'),
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
            'hash_height_number' => array(
                'params' => array('height'=>'500'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'min-height:500px'
                    ),
                ),
            ),
            'hash_height_px' => array(
                'params' => array('height'=>'400px'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'min-height:400px'
                    ),
                ),
            ),
            'hash_classname' => array(
                'params' => array('class'=>'testclass'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron testclass',
                    ),
                ),
            ),
            'hash_bg-color' => array(
                'params' => array('bg-color'=>'#ddd'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'background-color:#ddd',
                    ),
                ),
            ),
            'hash_bg-image' => array(
                'params' => array('bg-image'=>'image/test.png'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'background-image:url(image/test.png)',
                    ),
                ),
            ),
            'hash_color' => array(
                'params' => array('color'=>'#333'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron',
                        'style' => 'color:#333',
                    ),
                ),
            ),
            'hash_all_param' => array(
                'params' => array('align' => 'center','valign' => 'middle','height'=>'300px','color'=>'#555','bg-image'=>'image/hoge.png','bg-color'=>'#ddd'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'jumbotron text-center',
                        'style' => 'color:#555;background-image:url(image/hoge.png);background-color:#ddd;min-height:300px'
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
     * @dataProvider delimProvider
     */
    public function testDelimiterTest($params)
    {
        $body = "test1\n" . "\n\n++++\n\n" . "test2\n";
        $this->plugin = new SectionPlugin(new Toiee\HaikMarkdown\HaikMarkdown);
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'haik-plugin-section'
            ),
            'descendant' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'col-sm-6',
                ),
            )
        );
        
        $result = $this->plugin->convert($params, $body);
        $this->assertTag($expected, $result);
    }
    
    public function delimProvider()
    {
        return array(
            array(
              'params' => array( array('delim' => '++++'))
            ),
            array(
              'params' => array( array('delimiter' => '++++'))
            ),
            array(
              'params' => array( array('separator' => '++++'))
            ),
            array(
              'params' => array('sep' => '++++')
            ),
            array(
              'params' => array('delim' => '++++')
            ),
            array(
              'params' => array('delimiter' => '++++')
            ),
            array(
              'params' => array('separator' => '++++')
            ),
            array(
              'params' => array('sep' => '++++')
            ),
        );
    }

    public function testDelimiterWithNullTest()
    {
        $body = "test1\n" . "\n\n++++\n\n" . "test2\n";
        $this->plugin = new SectionPlugin(new Toiee\HaikMarkdown\HaikMarkdown);
        $params =  array('sep' => '');

        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'haik-plugin-section'
            ),
            'descendant' => array(
                'tag' => 'p',
                'content' => "++++",
            )
        );
        
        $result = $this->plugin->convert($params, $body);
        $this->assertTag($expected, $result);
    }


}
