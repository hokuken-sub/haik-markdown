<?php
use Toiee\HaikMarkdown\Plugin\Basic\Deco\DecoPlugin;

class DecoPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface');
        $this->plugin = new DecoPlugin($this->parser);
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
        $result = $this->plugin->inline($params, 'test');
        $this->assertTag($expected, $result);
    }

    public function paramsProvider()
    {
        return array(
            'none' => array(
                'deco' => array(),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array('class' => 'haik-plugin-deco'),
                    'content' => 'test'
                )
            ),
            'b' => array(
                'deco' => array('b'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array('class' => 'haik-plugin-deco'),
                    'child' => array(
                        'tag' => 'strong',
                        'content' => 'test'
                    )
                )
            ),
            'bold' => array(
                'deco' => array('bold'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array('class' => 'haik-plugin-deco'),
                    'child' => array(
                        'tag' => 'strong',
                        'content' => 'test'
                    )
                )
            ),
            'u' => array(
                'deco' => array('u'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'text-decoration:underline'
                    ),
                    'content' => 'test',
                )
            ),
            'bu' => array(
                'deco' => array('b','u'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'text-decoration:underline'
                    ),
                    'child' => array(
                        'tag' => 'strong',
                        'content' => 'test'
                    )
                )
            ),
            'color' => array(
                'deco' => array('#fff'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'color:#fff'
                    ),
                    'content' => 'test'
                )
            ),
            'bgcolor' => array(
                'deco' => array('', '#000'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'color:inherit;background-color:#000'
                    ),
                    'content' => 'test'
                )
            ),
            'b-color' => array(
                'deco' => array('b', '#fff'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'color:#fff'
                    ),
                    'child' => array(
                        'tag' => 'strong',
                        'content' => 'test'
                    )
                )
            ),        
        );
    }
}