<?php
use Hokuken\HaikMarkdown\Plugin\Basic\Deco\DecoPlugin;

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
            'font-size-only-numeric' => array(
                'deco' => array('18'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'font-size:18px'
                    ),
                    'content' => 'test'
                )
            ),
            'font-size-starts-with-dot' => array(
                'deco' => array('.8'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'font-size:.8'
                    ),
                    'content' => 'test'
                )
            ),
            'font-size-with-other-unit' => array(
                'deco' => array('1.6em'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'font-size:1.6em'
                    ),
                    'content' => 'test'
                )
            ),
            'font-size-with-string' => array(
                'deco' => array('small'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'font-size:small'
                    ),
                    'content' => 'test'
                )
            ),
            'italic' => array(
                'deco' => array('italic'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'font-style:italic'
                    ),
                    'content' => 'test'
                )
            ),
            // hash params
            '#b' => array(
                'deco' => array('b' => null),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array('class' => 'haik-plugin-deco'),
                    'child' => array(
                        'tag' => 'strong',
                        'content' => 'test'
                    )
                )
            ),
            '#bold' => array(
                'deco' => array('bold' => null),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array('class' => 'haik-plugin-deco'),
                    'child' => array(
                        'tag' => 'strong',
                        'content' => 'test'
                    )
                )
            ),
            '#u' => array(
                'deco' => array('u' => null),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'text-decoration:underline'
                    ),
                    'content' => 'test',
                )
            ),
            '#bu' => array(
                'deco' => array('b' => null,'u' => null),
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
            '#color' => array(
                'deco' => array('color' => '#fff'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'color:#fff'
                    ),
                    'content' => 'test'
                )
            ),
            '#bgcolor' => array(
                'deco' => array('bg-color' => '#000'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'background-color:#000'
                    ),
                    'content' => 'test'
                )
            ),
            '#b-color' => array(
                'deco' => array('b' => null, 'color' => '#fff'),
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
            '#font-size-only-numeric' => array(
                'deco' => array('size' => '18'),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'font-size:18px'
                    ),
                    'content' => 'test'
                )
            ),
            '#i' => array(
                'deco' => array('i' => null),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'font-style:italic'
                    ),
                    'content' => 'test'
                )
            ),
            '#italic' => array(
                'deco' => array('italic' => null),
                'assert' => array(
                    'tag' => 'span',
                    'attributes' => array(
                        'class' => 'haik-plugin-deco',
                        'style' => 'font-style:italic'
                    ),
                    'content' => 'test'
                )
            ),
        );
    }
}
