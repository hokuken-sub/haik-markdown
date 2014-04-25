<?php
use Hokuken\HaikMarkdown\HaikMarkdown;
use Hokuken\HaikMarkdown\Plugin\Basic\PluginRepository as BasicPluginRepository;
use Hokuken\HaikMarkdown\Plugin\Bootstrap\PluginRepository as BootstrapPluginRepository;

class HaikMarkdownIntegrationTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $parser = new HaikMarkdown();
        $basic_repository = new BasicPluginRepository($parser);
        $bootstrap_repository = new BootstrapPluginRepository($parser);
        $parser->registerPluginRepository($basic_repository)
               ->registerPluginRepository($bootstrap_repository);
        
        $this->parser = $parser;
    }

    public function testCallInlinePluginInConvertPlugin()
    {
        $markdown = '
::: cols

Time is /(icon time).

:::
';
        $expected = [
            'tag' => 'i',
            'attributes' => [
                'class' => 'haik-plugin-icon glyphicon glyphicon-time',
            ],
        ];
        $result = $this->parser->transform($markdown);
        $this->assertTag($expected, $result);
    }

    public function testCallPluginPluginInConvertPlugin()
    {
        $markdown = '
::: cols

:::: alert
Alert!
::::

:::
';
        $expected = [
            'tag' => 'div',
            'attributes' => [
                'class' => 'haik-plugin-alert alert alert-warning'
            ],
        ];
        $result = $this->parser->transform($markdown);
        $this->assertTag($expected, $result);
    }

    public function testAvailableReferenceStyleLinkInConvertPlugin()
    {
        $markdown = '

[Apple][apple]

::: {#cols}

[Google][google]
[Yahoo!][yahoo]

:::

[apple]: http://www.apple.com/
[google]: http://www.google.com/
[yahoo]: http://www.yahoo.co.jp/ "Yahoo! Japan"
';
        $result = $this->parser->transform($markdown);

        // google
        $expected = [
            'tag' => 'a',
            'attributes' => [
                'href' => 'http://www.google.com/'
            ],
            'content' => 'Google',
        ];
        $this->assertTag($expected, $result);

        // yahoo
        $expected = [
            'tag' => 'a',
            'attributes' => [
                'href' => 'http://www.yahoo.co.jp/',
                'title' => 'Yahoo! Japan'
            ],
            'content' => 'Yahoo',
        ];
        $this->assertTag($expected, $result);
    }

    public function testCallLinkNestedInlinePlugin()
    {
        $markdown = '
/[[link text](http://www.example.com/)](deco b)
';
        $result = $this->parser->transform($markdown);

        $expected = [
            'tag' => 'span',
            'attributes' => [
                'class' => 'haik-plugin-deco',
            ],
            'child' => [
                'tag' => 'strong',
                'child' => [
                    'tag' => 'a',
                    'attributes' => [
                        'href' => 'http://www.example.com/',
                    ],
                    'content' => 'link text'
                ],
            ]
        ];
        $this->assertTag($expected, $result);
    }

    public function testCallNestedInlinePlugin()
    {
        $markdown = '
/[/(icon smile) Greet!](button http://www.example.com/,primary,large)
';
        $result = $this->parser->transform($markdown);

        $expected = [
            'tag' => 'a',
            'attributes' => [
                'class' => 'haik-plugin-button btn btn-primary btn-lg',
                'href' => 'http://www.example.com/',
            ],
            'child' => [
                'attributes' => [
                    'class' => 'haik-plugin-icon glyphicon glyphicon-smile',
                ],
            ]
        ];
        $this->assertTag($expected, $result);        
    }

    public function testCallNestedConvertPlugin()
    {
        $markdown = '
:::section

:::: cols
cols
---
- 6
::::
section
:::
';
        $result = $this->parser->transform($markdown);
        $expected = [
            'tag' => 'p',
            'content' => 'section'
        ];
        $this->assertTag($expected, $result);
    }

    public function testCallNestedHeading()
    {
        $markdown = '
:::section

# Heading

:::
';
        $result = $this->parser->transform($markdown);
        $expected = [
            'tag' => 'h1',
            'content' => 'Heading',
        ];
        $this->assertTag($expected, $result);
    }

    public function testCallSingleLineConvertMultiply()
    {
        $markdown = '
:::section

:::: image http://www.example.com/hoge.jpg, hoge ::::

:::: image http://example.jp/fuga.png, fuga ::::

:::
';
        $result = $this->parser->transform($markdown);
        $expected = [
            'tag' => 'img',
            'attributes' => [
                'src' => 'http://www.example.com/hoge.jpg',
                'title' => 'hoge'
            ],
        ];
        $this->assertTag($expected, $result);

        $expected = [
            'tag' => 'img',
            'attributes' => [
                'src' => 'http://example.jp/fuga.png',
                'title' => 'fuga'
            ],
        ];
        $this->assertTag($expected, $result);
    }

    public function testDefineReferenceIdOfLinkAndImageAndPlugin()
    {
        $markdown = <<< EOM
This is the [link1], and [link 2][link2].

This is the ![with title][image1], and ![without title][image2].

This is the /[button][plugin1].

This is using confusable-plugin-referenced-link-define as link [link text][link3_confusable_plugin].
This is using confusable-plugin-referenced-link-define as plugin /[link text][link3_confusable_plugin].

::: [plugin2]
This is the contents.
:::

This is the [link 4][link4].


[link1]: http://www.example.com/ "Example"
[link2]: http://example.jp/
[image1]: http://example.jp/hoge.jpg "Title"
[image2]: http://www.example.com/fuga.png
[plugin1]: button http://www.example.com/, primary, large
[link3_confusable_plugin]: button
[plugin2]: section
---
bg-color: red
class: class-name
---
[link4]: http://www.example.com/whatsnew.html "New commers"
EOM;

        $result = $this->parser->transform($markdown);
        $expected = [
            'tag' => 'a',
            'attributes' => [
                'href' => 'http://www.example.com/',
                'title' => 'Example',
            ],
            'content' => 'link1',
        ];
        $this->assertTag($expected, $result);

        $expected = [
            'tag' => 'a',
            'attributes' => [
                'href' => 'http://example.jp/',
                'title' => '',
            ],
            'content' => 'link 2',
        ];
        $this->assertTag($expected, $result);

        $expected = [
            'tag' => 'img',
            'attributes' => [
                'src' => 'http://example.jp/hoge.jpg',
                'title' => 'Title',
                'alt' => 'with title'
            ],
        ];
        $this->assertTag($expected, $result);

        $expected = [
            'tag' => 'img',
            'attributes' => [
                'src' => 'http://www.example.com/fuga.png',
                'title' => '',
                'alt' => 'without title'
            ],
        ];
        $this->assertTag($expected, $result);

        $expected = [
            'tag' => 'a',
            'attributes' => [
                'href' => 'http://www.example.com/',
                'class' => 'btn btn-primary btn-lg',
            ],
            'content' => 'button'
        ];
        $this->assertTag($expected, $result);

        $expected = [
            'tag' => 'a',
            'attributes' => [
                'href' => 'button',
            ],
            'content' => 'link text'
        ];
        $this->assertTag($expected, $result);

        $not_expected = [
            'tag' => 'a',
            'attributes' => [
                'href' => 'button',
                'class' => 'btn btn-default',
            ],
            'content' => 'link text'
        ];
        $this->assertNotTag($not_expected, $result);

        $expected = [
            'tag' => 'div',
            'attributes' => [
                'class' => 'class-name',
            ],
            'child' => [
                'tag' => 'div',
                'attributes' => [
                    'style' => 'background-color:red',
                ],
            ],
        ];
        $this->assertTag($expected, $result);

        $expected = [
            'tag' => 'a',
            'attributes' => [
                'href' => 'http://www.example.com/whatsnew.html',
                'title' => 'New commers',
            ],
            'content' => 'link 4',
        ];
        $this->assertTag($expected, $result);

    }

}
