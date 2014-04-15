<?php

use Toiee\HaikMarkdown\HaikMarkdown;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Nav\NavPlugin;

class NavPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = new HaikMarkdown();
        $this->plugin = new NavPlugin($this->parser);
        $this->delimiter = '* * *';
    }

    public function testExistsConvertMethod()
    {
        $this->assertInternalType('string', $this->plugin->convert());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testThrowsWhenCallInlineMethod()
    {
        $this->plugin->inline();
    }

    /**
     * @dataProvider paramsProvider
     */
    public function testParams($params, $expected, $not_expected = null)
    {
        $parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('body');
            return $mock; 
        });
        $result = $this->plugin->convert($params);
        $this->assertTag($expected, $result);
        
        if ($not_expected)
        {
            $this->assertNotTag($not_expected, $result);
        }
    }

    public function paramsProvider()
    {
        return array(
            'default #1' => array(
                array(),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-default navbar-fixed-top',
                        'role' => 'navigation'
                    ),
                    'descendant' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'collapse navbar-collapse'
                        ),
                    ),
                )
            ),
            'default #2' => array(
                array(),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-default navbar-fixed-top',
                        'role' => 'navigation'
                    ),
                    'descendant' => array(
                        'tag' => 'button',
                        'attributes' => array(
                            'class' => 'navbar-toggle',
                            'type' => 'button',
                            'data-toggle' => 'collapse'
                        ),
                    ),
                )
            ),
            'default: top' => array(
                array(
                    'top'
                ),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-default navbar-fixed-top',
                        'role' => 'navigation'
                    )
                )
            ),
            'fixed bottom' => array(
                array(
                    'bottom'
                ),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-default navbar-fixed-bottom',
                        'role' => 'navigation'
                    )
                )
            ),
            'static top' => array(
                array(
                    'static'
                ),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-default navbar-static-top',
                        'role' => 'navigation'
                    )
                )
            ),
            'ignore static bottom' => array(
                array(
                    'static', 'bottom'
                ),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-default navbar-static-top',
                        'role' => 'navigation'
                    )
                )
            ),
            'disable responsive #1' => array(
                array(
                    'non-responsive'
                ),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-default navbar-fixed-top',
                        'role' => 'navigation'
                    )
                ),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-default navbar-fixed-top',
                        'role' => 'navigation'
                    ),
                    'descendant' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'collapse navbar-collapse'
                        )
                    )
                )
            ),
            'disable responsive #2' => array(
                array(
                    'non-responsive'
                ),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-default navbar-fixed-top',
                        'role' => 'navigation'
                    )
                ),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-default navbar-fixed-top',
                        'role' => 'navigation'
                    ),
                    'descendant' => array(
                        'tag' => 'button',
                        'attributes' => array(
                            'class' => 'navbar-toggle',
                            'type' => 'button',
                            'data-toggle' => 'collapse'
                        ),
                    ),
                )
            ),
            'inverse' => array(
                array(
                    'inverse'
                ),
                array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'navbar navbar-inverse navbar-fixed-top',
                        'role' => 'navigation'
                    )
                )
            ),
        );
    }

    /**
     * @dataProvider configTextProvider
     */
    public function testConfig($config_text, $attribute_name, $expected)
    {
        
        $body = "body\n{$this->delimiter}\n{$config_text}";
        $result = $this->plugin->convert(array(), $body);
        $this->assertAttributeEquals($expected, $attribute_name, $this->plugin);
    }

    public function configTextProvider()
    {
        return array(
            'default #1' => array(
                '',
                'brandTitle',
                '',
            ),
            'default #2' => array(
                '',
                'actionButtons',
                '',
            ),
            'default #2' => array(
                '',
                'hasBrandImage',
                false,
            ),
            'set brand title' => array(
                'BRAND: <a href="#">The Brand</a>',
                'brandTitle',
                '<a href="#" class="navbar-brand">The Brand</a>',
            ),
            'set brand image #1' => array(
                'BRAND: <a href="#"><img src="brand.png" alt="The Brand"></a>',
                'brandTitle',
                '<a href="#" class="navbar-brand"><img src="brand.png" alt="The Brand"></a>',
            ),
            'set brand image #2' => array(
                'BRAND: <a href="#"><img src="brand.png" alt="The Brand"></a>',
                'hasBrandImage',
                true,
            ),
            'set action button' => array(
                "ACTION:\n" . '<a href="#" class="btn btn-success">Sign Up</a>',
                'actionButtons',
                '<a href="#" class="btn btn-success navbar-btn navbar-right">Sign Up</a>',
            ),
            'set action buttons' => array(
                "ACTION:\n" . '<a href="#" class="btn btn-success">Sign Up</a>' . "\n" . '<a href="#" class="btn btn-success">Sign In</a>',
                'actionButtons',
                  '<div class="btn-group navbar-right">' . 
                    '<a href="#" class="btn btn-success navbar-btn">Sign Up</a>' . "\n" .
                    '<a href="#" class="btn btn-success navbar-btn">Sign In</a>' . "\n" . 
                  '</div>',
            ),
            'set brand title and image #1' => array(
                'BRAND: <a href="#"><img src="brand.png" alt="Brand Image"> The Brand</a>',
                'brandTitle',
                '<a href="#" class="navbar-brand"><img src="brand.png" alt="Brand Image"> The Brand</a>',
            ),
            'set brand title and image #1' => array(
                'BRAND: <a href="#"><img src="brand.png" alt="Brand Image"> The Brand</a>',
                'hasBrandImage',
                true,
            ),
            'set brand title and action buttons #1' => array(
                'BRAND: <a href="#">The Brand</a>' . "\n" .
                'ACTION:' . "\n". '<a href="#" class="btn btn-success">Sign Up</a>',
                'brandTitle',
                '<a href="#" class="navbar-brand">The Brand</a>',
            ),
            'set brand title and action buttons #2' => array(
                'BRAND: <a href="#">The Brand</a>' . "\n" .
                'ACTION:' . "\n". '<a href="#" class="btn btn-success">Sign Up</a>',
                'actionButtons',
                '<a href="#" class="btn btn-success navbar-btn navbar-right">Sign Up</a>',
            ),
            'set action buttons and brand title ordered inverse' => array(
                'ACTION:' . "\n". '<a href="#" class="btn btn-success">Sign Up</a>' . "\n" .
                                  '<a href="#" class="btn btn-primary">Sign In</a>' . "\n" .
                'BRAND: <a href="#">The Brand</a>' . "\n",
                'actionButtons',
                '<div class="btn-group navbar-right">' .
                  '<a href="#" class="btn btn-success navbar-btn">Sign Up</a>' . "\n" .
                  '<a href="#" class="btn btn-primary navbar-btn">Sign In</a>' . "\n" .
                '</div>',
            ),
            'test invalid: action button and other text' => array(
                "ACTION:\n" . '<a href="#" class="btn btn-success">Sign Up</a>foo bar buzz',
                'actionButtons',
                '<a href="#" class="btn btn-success navbar-btn navbar-right">Sign Up</a>',
            ),
            'test invalid: action button and other tags #1' => array(
                "ACTION:\n" . '<strong><a href="#" class="btn btn-success">Sign Up</a></strong>',
                'actionButtons',
                '<a href="#" class="btn btn-success navbar-btn navbar-right">Sign Up</a>',
            ),
            'test invalid: action button and other tags #2' => array(
                "ACTION:\n" . '<p><a href="#" class="btn btn-success">Sign Up</a>'."\n".'<small>foo bar buzz</small></p>',
                'actionButtons',
                '<a href="#" class="btn btn-success navbar-btn navbar-right">Sign Up</a>',
            ),
        );
    }

    /**
     * @dataProvider delimiterProvider
     */
    public function testDelimiter($delimiter)
    {
        $body = 'body' . "\n" . $delimiter . "\n";
        $body.= 'BRAND: <a href="#">The Brand</a>';
        $result = $this->plugin->convert(array(), $body);
        $expected = array(
            'tag' => 'a',
            'attributes' => array(
                'class' => 'navbar-brand',
            ),
        );
        $this->assertTag($expected, $result);
    }

    public function delimiterProvider()
    {
        return [
            [
                '* * *'
            ],
            [
                '* * * *'
            ],
            [
                '* * * * '
            ],
            [
                '* * * * *'
            ],
        ];
    }

    /**
     * @dataProvider bodyProvider
     */
    public function testBody($params, $body, $expects, $not_expects = array())
    {
        $result = $this->plugin->convert($params, $body);
        var_dump($result);
        foreach ($expects as $expected)
        {
            if ( ! $expected) continue;
            $this->assertTag($expected, $result);
        }
        foreach ($not_expects as $not_expected)
        {
            if ( ! $not_expected) continue;
            $this->assertNotTag($not_expected, $result);
        }
    }

    public function bodyProvider()
    {
        return array(
            'normal list items' => array(
                array(),
                '
<ul>
    <li><a href="#">Item1</a></li>
    <li><a href="#">Item2</a></li>
    <li><a href="#">Item3</a></li>
</ul>
',
                array(
                    array(
                        'tag' => 'ul',
                        'attributes' => array(
                            'class' => 'nav navbar-nav navbar-right'
                        ),
                        'children' => array(
                            'only' => array(
                                'tag' => 'li',
                            ),
                            'count' => 3
                        ),
                        'parent' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'collapse navbar-collapse'
                            )
                        ),
                        'ancestor' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'navbar navbar-default navbar-fixed-top',
                                'role' => 'navigation',
                            )
                        ),
                    ),
                ),
                array(
                    array(
                    ),
                )
            ),

            'normal list items with .active' => array(
                array(),
                '
<ul>
    <li class="active"><a href="#">Item1</a></li>
    <li><a href="#">Item2</a></li>
    <li><a href="#">Item3</a></li>
</ul>
',
                array(
                    array(
                        'tag' => 'ul',
                        'attributes' => array(
                            'class' => 'nav navbar-nav navbar-right'
                        ),
                        'child' => array(
                            'tag' => 'li',
                            'attributes' => array('class' => 'active'),
                        ),
                        'parent' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'collapse navbar-collapse'
                            )
                        ),
                    ),
                ),
                array(
                    array(
                    ),
                )
            ),

            'with nested lists' => array(
                array(),
                '
<ul>
    <li><a href="#">Item1</a></li>
    <li><a href="#">Item2</a></li>
    <li>
        <a href="#">Item3</a>
        <ul>
            <li><a href="#">Sub Item1</a></li>
            <li><a href="#">Sub Item2</a></li>
            <li>----</li>
            <li><a href="#">Sub Item2</a></li>
        </ul>
    </li>
</ul>
',
                array(
                    array(
                        'tag' => 'ul',
                        'attributes' => array(
                            'class' => 'nav navbar-nav navbar-right'
                        ),
                        'children' => array(
                            'only' => array(
                                'tag' => 'li',
                            ),
                            'count' => 3
                        ),
                        'parent' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'collapse navbar-collapse'
                            )
                        ),
                    ),
                    array(
                        'tag' => 'ul',
                        'attributes' => array(
                            'class' => 'dropdown-menu'
                        ),
                        'parent' => array(
                            'tag' => 'li',
                            'attributes' => array(
                                'class' => 'dropdown',
                            )
                        ),
                        'children' => array(
                            'only' => array(
                                'tag' => 'li',
                            ),
                            'count' => 4,
                        ),
                    ),
                    array(
                        'tag' => 'li',
                        'attributes' => array(
                            'class' => 'divider',
                        ),
                        'content' => '',
                        'parent' => array(
                            'tag' => 'ul',
                            'attributes' => array('class' => 'dropdown-menu'),
                        ),
                    ),
                ),
                array(
                    array(
                    ),
                )
            ),

            'text only' => array(
                array(),
                'Lorem ipsum doler sit amet.',
                array(
                    array(
                        'tag' => 'p',
                        'attributes' => array(
                            'class' => 'navbar-text navbar-right'
                        ),
                        'parent' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'collapse navbar-collapse',
                            ),
                        ),
                    )
                ),
                array(
                
                ),
            ),

            'list and text' => array(
                array(),
                '
<ul>
    <li class="active"><a href="#">Item1</a></li>
    <li><a href="#">Item2</a></li>
    <li><a href="#">Item3</a></li>
</ul>

<p>Lorem ipsum doler sit amet.</p>
',
                array(
                    array(
                        'tag' => 'ul',
                        'attributes' => array(
                            'class' => 'nav navbar-nav navbar-right'
                        ),
                        'children' => array(
                            'only' => array(
                                'tag' => 'li',
                            ),
                            'count' => 3
                        ),
                        'parent' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'collapse navbar-collapse'
                            )
                        ),
                        'ancestor' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'navbar navbar-default navbar-fixed-top',
                                'role' => 'navigation',
                            )
                        ),
                    ),
                ),
                array(
                    // p is ignore
                    array(
                        'tag' => 'p',
                        'parent' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'collapse navbar-collapse',
                            ),
                        ),
                    ),
                ),
            ),

            'normal list item without link #1' => array(
                array(),
                '
<ul>
    <li>Item1</li>
    <li><a href="#">Item2</a></li>
    <li><a href="#">Item3</a></li>
</ul>
',
                array(
                    array(
                        'tag' => 'ul',
                        'attributes' => array(
                            'class' => 'nav navbar-nav navbar-right'
                        ),
                        'children' => array(
                            'only' => array(
                                'tag' => 'li',
                            ),
                            'count' => 2
                        ),
                        'parent' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'collapse navbar-collapse'
                            )
                        ),
                        'ancestor' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'navbar navbar-default navbar-fixed-top',
                                'role' => 'navigation',
                            )
                        ),
                    ),
                ),
                array(
                    array(
                    ),
                )
            ),

            'normal list item without link #2' => array(
                array(),
                '
<ul>
    <li>Item1</li>
</ul>
',
                array(
                    array(
                        'tag' => 'ul',
                        'attributes' => array(
                            'class' => 'nav navbar-nav navbar-right'
                        ),
                        'parent' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'collapse navbar-collapse'
                            )
                        ),
                        'ancestor' => array(
                            'tag' => 'div',
                            'attributes' => array(
                                'class' => 'navbar navbar-default navbar-fixed-top',
                                'role' => 'navigation',
                            )
                        ),
                    ),
                ),
                array(
                    array(
                        'tag' => 'li',
                        'parent' => array(
                            'tag' => 'ul',
                            'attributes' => array(
                                'class' => 'nav navbar-nav navbar-right',
                            ),
                        ),
                    ),
                )
            ),



        );
    }

    /**
     * @dataProvider htmlProvider
     */
    public function testHtml($params, $body, $config_text, $expects, $not_expects)
    {
        $body .= "\n". $this->delimiter . "\n" . $config_text;
        $result = $this->plugin->convert($params, $body);

        foreach ($expects as $expected)
        {
            $this->assertTag($expected, $result);
        }
        foreach ($not_expects as $not_expected)
        {
            $this->assertNotTag($not_expected, $result);
        }
        
    }

    public function htmlProvider()
    {
        return [
            //no brand, no actions
            'default' => [
                [],
                '',
                '',
                [
                ],
                [
                    [
                        'tag' => 'a',
                        'attributes' => [
                            'class' => 'navbar-brand',
                        ],
                    ],
                    [
                        'tag' => 'a',
                        'attributes' => [
                            'class' => 'navbar-btn',
                        ],
                    ],
                ]
            ],
            'with_action_buttons' => [
                [],
                '',
                '
<a href="#" class="btn btn-success">Sign Up</a>
',
                [
                    [
                        'tag' => 'a',
                        'attributes' => [
                            'class' => 'btn btn-success navbar-btn navbar-right',
                        ],
                        'parent' => [
                            'tag' => 'div',
                            'attributes' => [
                                'class' => 'collapse navbar-collapse',
                            ],
                        ],
                        'ancestor' => [
                            'tag' => 'div',
                            'attributes' => [
                                'class' => 'navbar navbar-default navbar-fixed-top'
                            ],
                        ],
                    ],
                ],
                []
            ],
            'with brand title' => [
                [],
                '',
                '
BRAND: The Brand
',
                [
                    [
                        'tag' => 'a',
                        'attributes' => [
                            'class' => 'navbar-brand',
                            'content' => 'The Brand',
                        ],
                        'parent' => [
                            'tag' => 'div',
                            'attributes' => [
                                'class' => 'navbar-header',
                            ],
                        ],
                    ],
                ],
                []
            ],
            'with brand image' => [
                [],
                '',
                '
BRAND: <img src="brand.png" alt="The Brand">
',
                [
                    [
                        'tag' => 'a',
                        'attributes' => [
                            'class' => 'navbar-brand',
                            'content' => 'The Brand',
                        ],
                        'parent' => [
                            'tag' => 'div',
                            'attributes' => [
                                'class' => 'navbar-header',
                                'style' => 'padding:0',
                            ],
                        ],
                        'child' => [
                            'tag' => 'img',
                            'attributes' => [
                                'src' => 'brand.png',
                                'alt' => 'The Brand',
                            ],
                        ],
                    ],
                ],
                []
            ],


            'multi action buttons' => [
                [],
                '',
                '
ACTION:
<a href="#" class="btn btn-success">Action1</a>
<a href="#" class="btn btn-success">Action2</a>
',
                [
                    [
                        'tag' => 'a',
                        'attributes' => [
                            'class' => 'btn btn-success navbar-btn',
                            'content' => 'Action1',
                        ],
                        'parent' => [
                            'tag' => 'div',
                            'attributes' => [
                                'class' => 'btn-group navbar-right',
                            ],
                        ],
                    ],
                    [
                        'tag' => 'a',
                        'attributes' => [
                            'class' => 'btn btn-success navbar-btn',
                            'content' => 'Action2',
                        ],
                        'parent' => [
                            'tag' => 'div',
                            'attributes' => [
                                'class' => 'btn-group navbar-right',
                            ],
                        ],
                    ],
                ],
                []
            ],

        ];
    }

}
