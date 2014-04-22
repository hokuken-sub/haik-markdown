<?php
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Label\LabelPlugin;

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
            ),
            'info' => array(
                'label' => array('info'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-info'
                    )
                ),
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
            // #hash params
            '#type:info' => array(
                'label' => array('type' => 'info'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-info'
                    )
                ),
            ),
            '#type:default' => array(
                'label' => array('type' => 'default'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-default'
                    )
                ),
            ),
            '#type:success' => array(
                'label' => array('type' => 'success'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-success'
                    )
                ),
            ),
            '#type:primary' => array(
                'label' => array('type' => 'primary'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-primary'
                    )
                ),
            ),
            '#type:warning' => array(
                'label' => array('type' => 'warning'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-warning'
                    )
                ),
            ),
            '#type:danger' => array(
                'label' => array('type' => 'danger'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-danger'
                    )
                ),
            ),
            '#class' => array(
                'label' => array('class' => 'class-name'),
                'expected' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-label label label-default class-name'
                    )
                ),
            ),
            '#type, #class' => array(
                'label' => array('type' => 'danger', 'class' => 'class-name'),
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
