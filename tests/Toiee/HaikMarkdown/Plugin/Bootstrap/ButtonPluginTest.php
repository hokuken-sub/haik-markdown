<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Button\ButtonPlugin;

class ButtonPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface');
        $this->plugin = new ButtonPlugin($this->parser);
    }

    public function testInlineMethodExists()
    {
        $this->assertInternalType('string', $this->plugin->inline());
    }

    /**
     * @dataProvider paramsProvider
     */
    public function testParameter($params, $expected)
    {
        $this->assertTag($expected, $this->plugin->inline($params, 'button'));
    }

    public function paramsProvider()
    {
        return array(
            'none' => array(
                'button' => array(),
                'expected' => array(
                    'tag' => 'a',
                    'attributes' => array(
                        'class' => 'haik-plugin-button btn btn-default',
                        'href' => '#'
                    ),
                    'content' => 'button',
                ),
            ),
            'url' => array(
                'button' => array('http://www.example.com/'),
                'expected' => array(
                    'tag' => 'a',
                    'attributes' => array(
                        'class' => 'haik-plugin-button btn btn-default',
                        'href' => 'http://www.example.com/'
                    ),
                    'content' => 'button',
                ),
            ),
            'primary' => array(
                'button' => array('#', 'primary'),
                'expected' => array(
                    'tag' => 'a',
                    'attributes' => array(
                        'class' => 'haik-plugin-button btn btn-primary',
                        'href' => '#'
                    ),
                    'content' => 'button',
                ),
            ),
            'large' => array(
                'button' => array('#', 'large'),
                'expected' => array(
                    'tag' => 'a',
                    'attributes' => array(
                        'class' => 'haik-plugin-button btn btn-default btn-lg',
                        'href' => '#'
                    ),
                    'content' => 'button',
                ),
            ),
            'small' => array(
                'button' => array('#', 'small'),
                'expected' => array(
                    'tag' => 'a',
                    'attributes' => array(
                        'class' => 'haik-plugin-button btn btn-default btn-sm',
                        'href' => '#'
                    ),
                    'content' => 'button',
                ),
            ),
            'mini' => array(
                'button' => array('#', 'mini'),
                'expected' => array(
                    'tag' => 'a',
                    'attributes' => array(
                        'class' => 'haik-plugin-button btn btn-default btn-xs',
                        'href' => '#'
                    ),
                    'content' => 'button',
                ),
            ),
            'block' => array(
                'button' => array('#', 'block'),
                'expected' => array(
                    'tag' => 'a',
                    'attributes' => array(
                        'class' => 'haik-plugin-button btn btn-default btn-block',
                        'href' => '#'
                    ),
                    'content' => 'button',
                ),
            ),
            'custom' => array(
                'button' => array('#', 'custom'),
                'expected' => array(
                    'tag' => 'a',
                    'attributes' => array(
                        'class' => 'haik-plugin-button btn btn-default custom',
                        'href' => '#'
                    ),
                    'content' => 'button',
                ),
            ),        
        );
    }

    public function testConvert()
    {
        $params = array('http://www.example.com/', 'primary');
        $result = $this->plugin->convert($params, 'button');
        $expected = array(
            'tag' => 'a',
            'content' => 'button',
            'attributes' => array(
                'href'  => 'http://www.example.com/',
                'class' => 'haik-plugin-button btn btn-primary btn-lg btn-block'
            ),
        );
    }
}