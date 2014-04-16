<?php
use Toiee\HaikMarkdown\HaikMarkdown;
use Toiee\HaikMarkdown\Plugin\Repositories\BasicPluginRepository;
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
::: {#cols}

Time is &icon(time);.

:::
';
        $expected = [
            'tag' => 'i',
            'attributes' => [
                'class' => 'haik-plugin-icon glyphicon glyphicon-time'
            ],
        ];
        $result = $this->parser->transform($markdown);
        $this->assertTag($expected, $result);
    }

    public function testCallPluginPluginInConvertPlugin()
    {
        $markdown = '
::: {#cols}

:::: {#alert}
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

}
