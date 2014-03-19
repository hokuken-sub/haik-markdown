<?php
use Toiee\HaikMarkdown\HaikMarkdown;
use Toiee\HaikMarkdown\Plugin\Repositories\PluginRepository;

class HaikMarkdownTest extends PHPUnit_Framework_TestCase {

    public function setup()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('inline')->andReturn('<span>inline plugin</span>');
            $mock->shouldReceive('convert')->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')
                 ->once()
                 ->andReturn(true);
            $mock->shouldReceive('load')
                 ->once()
                 ->andReturn($plugin_mock);
            return $mock;
        });

        $this->parser = new HaikMarkdown;
        $this->parser->setPluginRepository($repository);
    }

    public function testEmptyElementSuffix()
    {
        $this->assertEquals('>', $this->parser->empty_element_suffix);
    }

    public function testCodeClassPrefix()
    {
        $this->assertEquals('', $this->parser->code_class_prefix);
    }

    // ! inline plugin

    public function inlinePluginTestProvider()
    {
        return array(
            'plugin_name_only' => array(
                'markdown' => '&plugin;',
                'expected'   => '<p><span>inline plugin</span></p>',
            ),
            'plugin_name_and_params' => array(
                'markdown' => '&plugin(param1,param2);',
                'expected'   => '<p><span>inline plugin</span></p>',
            ),
            'plugin_name_and_body' => array(
                'markdown' => '&plugin{body};',
                'expected'   => '<p><span>inline plugin</span></p>',
            ),
            'plugin_name_and_params_and_body' => array(
                'markdown' => '&plugin(param1,param2){body};',
                'expected'   => '<p><span>inline plugin</span></p>',
            ),
        );
    }

    /**
     * @dataProvider inlinePluginTestProvider
     */
    public function testCallInlinePluginsWithAllVariations($markdown, $expected)
    {
        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallInlinePluginWithParams()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('inline')
                 ->with(array('param1','param2'), '')
                 ->andReturn('<span>inline plugin</span>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = '&plugin(param1,param2);';
        $expected = '<p><span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallInlinePluginWithBody()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('inline')
                 ->with(array(), 'body')
                 ->andReturn('<span>inline plugin</span>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = '&plugin{body};';
        $expected = '<p><span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallInlinePluginWithParamsAndBody()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('inline')
                 ->with(array('param1', 'param2'), 'body')
                 ->andReturn('<span>inline plugin</span>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = '&plugin(param1,param2){body};';
        $expected = '<p><span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallInlinePluginWithParamsContainsDoubleQuotes()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('inline')
                 ->with(array('param,1', 'param2,'), '')
                 ->andReturn('<span>inline plugin</span>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = '&plugin("param,1","param2,");';
        $expected = '<p><span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallInlinePluginWithParamsContainsEscapedDoubleQuotes()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('inline')
                 ->with(array('param"1"', 'param2'), '')
                 ->andReturn('<span>inline plugin</span>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = '&plugin("param""1""","param2");';
        $expected = '<p><span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));

        $markdown = '&plugin(param"1",param2);';
        $expected = '<p><span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallNestedInlinePlugins()
    {
        $this->markTestIncomplete('I do not know how to test nested plugins');
    }
    
    public function testCallInlinePluginTwiceInAParagraph()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('inline')
                 ->with(array('param"1"', 'param2'), '')
                 ->andReturn('<span>inline plugin</span>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = '&plugin(param);foo&plugin(param);';
        $expected = '<p><span>inline plugin</span>foo<span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));

    }

    public function testCallInlinePluginWithNotExistedName()
    {
        $repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock)
        {
            $mock->shouldReceive('exists')
                 ->once()
                 ->andReturn(false);
            return $mock;
        });
        $this->parser->setPluginRepository($repository);
    
        $markdown = '&plugin;hr&plugin;';
        $expected = '<p>&plugin;hr&plugin;</p>';
        
        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    // ! convert plugin

    public function convertPluginTestProvider()
    {
        return array(
            'plugin_name_only' => array(
                'markdown' => '{#plugin}',
                'expected' => '<div>convert plugin</div>',
            ),
            'plugin_name_and_params' => array(
                'markdown' => '{#plugin(param1,param2)}',
                'expected' => '<div>convert plugin</div>',
            ),
            'plugin_name_and_body' => array(
                'markdown' => ":::{#plugin}\nbody\n:::",
                'expected' => '<div>convert plugin</div>',
            ),
            'plugin_name_and_params_and_body' => array(
                'markdown' => ":::{#plugin(param1,param2)}\nbody\n:::",
                'expected' => '<div>convert plugin</div>',
            ),
        );        
    }

    /**
     * @dataProvider convertPluginTestProvider
     */
    public function testCallConvertPluginWithAllVariations($markdown, $expected)
    {
        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }
    
    public function testCallConvertPluginWithParams()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('convert')
                 ->with(array('param"1"', 'param2'), '')
                 ->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = '{#plugin(param1,param2)}';
        $expected = '<div>convert plugin</div>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));    }
    
    public function testCallConvertPluginWithBody()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('convert')
                 ->with(array(), "body\n")
                 ->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = ":::{#plugin}\nbody\n:::";
        $expected = '<div>convert plugin</div>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));    }
    
    public function testCallConvertPluginWithParamsAndBody()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('convert')
                 ->with(array('param1', 'param2'), "body\n")
                 ->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = ":::{#plugin(param1,param2)}\nbody\n:::";
        $expected = '<div>convert plugin</div>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallConvertPluginWithParamsContainsDoubleQuotes()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('convert')
                 ->with(array('param,1', 'param2,'), '')
                 ->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = '{#plugin("param,1","param2,")}';
        $expected = '<div>convert plugin</div>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallConvertPluginWithParamsContainsEscapedDoubleQuotes()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('convert')
                 ->with(array('param"1"', 'param2'), '')
                 ->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = '{#plugin("param""1""","param2")}';
        $expected = '<div>convert plugin</div>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));

        $markdown = '{#plugin(param"1",param2)}';
        $expected = '<div>convert plugin</div>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }
    
    public function testCallConvertPluginTwice()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('convert')
                 ->with(array(), '')
                 ->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $this->pluginMock = $plugin_mock;

        $markdown = "{#plugin}\n{#plugin}";
        $expected = "<div>convert plugin</div>\n\n<div>convert plugin</div>";

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));

    }

    public function testCallConvertPluginWithNotExistedName()
    {
        $repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock)
        {
            $mock->shouldReceive('exists')
                 ->once()
                 ->andReturn(false);
            return $mock;
        });
        $this->parser->setPluginRepository($repository);

        $markdown = "::: {#plugin}\nhoge\n:::";
        $expected = "<p>::: {#plugin}\nhoge\n:::</p>";
        
        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }
    // !TODO: 具体クラスでテストする
    public function testCallNestedConvertPlugins()
    {
        $this->markTestIncomplete('I do not know how to test nested plugins');
    }

}
