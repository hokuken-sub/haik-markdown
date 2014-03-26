<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Label\LabelPlugin;

class LabelPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface');
        $this->plugin = new LabelPlugin($this->parser);
    }

    public function testInlineMethodExists()
    {
        $this->assertInternalType('string',$this->plugin->inline());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsExceptionWhenCallConvert()
    {
        $this->plugin->convert();
    }
    
    /**
     * @dataProvider paramProvider
     */
    public function testParameter($params, $expected)
    {
        $result = $this->plugin->inline($params, 'body');
        $this->assertTag($expected, $result);

    }

    public function paramProvider()
    {
        return array(
            'default' => array(
                'label' => array(),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-default'
                    )
                ),
                'assert' => '<span class="haik-plugin-label label label-default">test</span>',
            ),
            'info' => array(
                'label' => array('info'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-info'
                    )
                ),
                'assert' => '<span class="haik-plugin-label label label-info">test</span>',
            ),
            'custom_class' => array(
                'label' => array('class-name'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-default class-name'
                    )
                ),
            ),
            'danger-with-custom_class' => array(
                'label' => array('danger', 'class-name'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-danger class-name'
                    )
                ),
            ),
        );
    }

    public function testEscapeClassAttribute()
    {
        $params = array('<custom>');
        $result = $this->plugin->inline($params, 'body');
        $pos = strpos($result, '&lt;custom&gt;');
        $this->assertTrue(!! $pos);
    }

}
