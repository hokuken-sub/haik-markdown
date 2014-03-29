<?php
use Toiee\HaikMarkdown\Plugin\Pure\Button\ButtonPlugin;

class PureButtonPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface');
        $this->plugin = new ButtonPlugin($this->parser);
    }

    public function testInlineMethodExists()
    {
        $this->assertInternalType('string', $this->plugin->inline());
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
        $result = $this->plugin->inline($params, 'button');
        $this->assertTag($expected, $result);
    }

    public function paramsProvider()
    {
        return array(
            'none' => array(
                'button' => array(),
                'expected' => array(
                    'tag' => 'a',
                    'attributes' => array(
                        'class' => 'haik-plugin-button pure-button',
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
                        'class' => 'haik-plugin-button pure-button',
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
                        'class' => 'haik-plugin-button pure-button pure-button-primary',
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
                        'class' => 'haik-plugin-button pure-button custom',
                        'href' => '#'
                    ),
                    'content' => 'button',
                ),
            ),        
        );
    }

}
