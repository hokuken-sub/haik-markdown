<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Alert\AlertPlugin;

class AlertPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('<div>test</div>');
            return $mock;
        });
        $this->plugin = new AlertPlugin($this->parser);
    }
    public function testConvertMethodExists()
    {
        $this->assertInternalType('string', $this->plugin->convert());
    }

    /**
     * @dataProvider paramsProvider
     */
    public function testParameter($params, $expected)
    {
        $result = $this->plugin->convert($params, 'test');
        $this->assertTag($expected, $result);
    }

    public function paramsProvider()
    {
        return array(
            'no_params' => array(
                'alert' => array(),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'haik-plugin-alert alert alert-warning'
                    ),
                ),
            ),
            'success' => array(
                'alert' => array('success'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'haik-plugin-alert alert alert-success'
                    ),
                ),
            ),
            'custom_class' => array(
                'alert' => array('class-name'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'haik-plugin-alert alert alert-warning class-name'
                    ),
                ),
            ),
            'with_close' => array(
                'alert' => array('info', 'close'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'haik-plugin-alert alert alert-info alert-dismissable'
                    ),
                    'child' => array(
                        'tag' => 'button',
                        'attributes' => array(
                            'class' => 'close',
                            'data-dismiss' => 'alert',
                            'aria-hidden' => 'true',
                        ),
                        'content' => '×'
                    ),
                ),
            ),
            // hash params
            'success' => array(
                'alert' => array('type' => 'success'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'haik-plugin-alert alert alert-success'
                    ),
                ),
            ),
            'custom_class' => array(
                'alert' => array('class' => 'class-name'),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'haik-plugin-alert alert alert-warning class-name'
                    ),
                ),
            ),
            'with_close' => array(
                'alert' => array('type' => 'info', 'close'=>null),
                'expected' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'haik-plugin-alert alert alert-info alert-dismissable'
                    ),
                    'child' => array(
                        'tag' => 'button',
                        'attributes' => array(
                            'class' => 'close',
                            'data-dismiss' => 'alert',
                            'aria-hidden' => 'true',
                        ),
                        'content' => '×'
                    ),
                ),
            ),
        );
    }

    public function testEscapeClassAttribute()
    {
        $params = array('<custom>');
        $result = $this->plugin->convert($params, 'body');
        $pos = strpos($result, '&lt;custom&gt;');
        $this->assertTrue(!! $pos);
    }

}