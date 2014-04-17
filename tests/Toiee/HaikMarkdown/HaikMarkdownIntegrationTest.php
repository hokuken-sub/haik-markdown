<?php
use Toiee\HaikMarkdown\HaikMarkdown;
use Toiee\HaikMarkdown\Plugin\Basic\PluginRepository as BasicPluginRepository;
use Toiee\HaikMarkdown\Plugin\Bootstrap\PluginRepository as BootstrapPluginRepository;

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

}
