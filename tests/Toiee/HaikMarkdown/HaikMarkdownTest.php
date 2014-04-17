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

        $this->parser = new HaikMarkdown();
        $this->parser->registerPluginRepository($repository);
    }

    public function testTransform()
    {
        $this->assertInternalType('string', $this->parser->transform(''));
    }

    public function testEmptyElementSuffix()
    {
        $this->assertEquals('>', $this->parser->empty_element_suffix);
    }

    public function testCodeClassPrefix()
    {
        $this->assertEquals('', $this->parser->code_class_prefix);
    }

    public function testBreakLineAlways()
    {
        $parser = new HaikMarkdown();
        $parser->setHardWrap(true);
        $markdown = "1\n2\n3";
        $expected = array(
            'tag' => 'p',
            'children' => array(
                'count' => 2,
                'only' => array('tag'=>'br')
            )
        );
        $result = $parser->transform($markdown);
        $this->assertTag($expected, $result);
    }

    public function testPluginRepository()
    {
        $this->assertTrue($this->parser->hasPlugin('plugin'));

        $plugin = $this->parser->loadPlugin('plugin');
        $this->assertInstanceOf('\Toiee\HaikMarkdown\Plugin\PluginInterface', $plugin);
    }

    public function testHasPluginReturnsFalseWhenPluginIsNotExist()
    {
        $parser = new HaikMarkdown();
        $repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock)
        {
            $mock->shouldReceive('exists')->andReturn(false);
            return $mock;
        });
        $parser->registerPluginRepository($repository);
        $this->assertFalse($parser->hasPlugin('plugin_not_exist'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsWhenLoadedNonExistancePlugin()
    {
        $repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock)
        {
            $mock->shouldReceive('exists')->andReturn(false);
            return $mock;
        });
        $parser = new HaikMarkdown();
        $parser->registerPluginRepository($repository);
        
        $parser->loadPlugin('plugin');
    }

    public function testPluginRepositoryGetAll()
    {
        $repository1 = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock)
        {
            $mock->shouldReceive('getAll')->andReturn(array('foo', 'bar', 'buzz', 'same'));
            return $mock;
        });
        $repository2 = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock)
        {
            $mock->shouldReceive('getAll')->andReturn(array('hoge', 'fuga', 'piyo', 'same'));
            return $mock;
        });
        $parser = new HaikMarkdown();
        $parser->registerPluginRepository($repository1)->registerPluginRepository($repository2);
        $this->assertAttributeEquals(array($repository2, $repository1), 'pluginRepositories', $parser);

        $expected = array('bar', 'buzz', 'foo', 'fuga', 'hoge', 'piyo', 'same');
        $plugins = $parser->getAllPlugin();
        $this->assertEquals($expected, $plugins);
    }

    // ! inline plugin

    public function inlinePluginTestProvider()
    {
        return array(
            'plugin_name_only' => array(
                'markdown' => '/(plugin)',
                'expected'   => '<p><span>inline plugin</span></p>',
            ),
            'plugin_name_and_params' => array(
                'markdown' => '/(plugin param1,param2)',
                'expected'   => '<p><span>inline plugin</span></p>',
            ),
            'plugin_name_and_body' => array(
                'markdown' => '/[body](plugin)',
                'expected'   => '<p><span>inline plugin</span></p>',
            ),
            'plugin_name_and_params_and_body' => array(
                'markdown' => '/[body](plugin param1,param2)',
                'expected'   => '<p><span>inline plugin</span></p>',
            ),
            'plugin_name_and_body_contains_white_space' => array(
                'markdown' => '/[body] (plugin)',
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

    public function testCallInlinePluginWithCSVParams()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('inline')
                 ->with(array('param1','param2'), '')
                 ->andReturn('<span>inline plugin</span>');
            return $mock;
        });
        $plugin_repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->andReturn($plugin_mock);
            return $mock;
        });
        $this->parser->registerPluginRepository($plugin_repository);

        $markdown = '/(inline param1,param2)';
        $expected = '<p><span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallInlinePluginWithYAMLHashParams()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('inline')
                 ->with(array('param1'=>'foo','param2'=>'bar'), '')
                 ->andReturn('<span>inline plugin</span>');
            return $mock;
        });
        $plugin_repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->andReturn($plugin_mock);
            return $mock;
        });
        $this->parser->registerPluginRepository($plugin_repository);

        $markdown = '/(inline param1: foo, param2: bar)';
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

        $markdown = '/[body](plugin)';
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

        $markdown = '/[body](plugin param1,param2)';
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

        $markdown = '/(plugin "param,1","param2,")';
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

        $markdown = '/(plugin "param""1""","param2")';
        $expected = '<p><span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));

        $markdown = '/(plugin param"1",param2)';
        $expected = '<p><span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallNestedInlinePlugins()
    {
        $expected = '<span class="inline"><i>icon</i></span>';
        $inline_plugin1 = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock) use ($expected)
        {
            $mock->shouldReceive('inline')->with(array(), '<i>icon</i>')->andReturn($expected);
            return $mock;
        });
        $inline_plugin2 = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('inline')->andReturn('<i>icon</i>');
            return $mock;
        });
        $plugin_repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($inline_plugin1, $inline_plugin2)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->once()->andReturn($inline_plugin2);
            $mock->shouldReceive('load')->twice()->andReturn($inline_plugin1);
            return $mock;
        });
        $parser = new HaikMarkdown();
        $parser->registerPluginRepository($plugin_repository);
        $text = trim($parser->transform('/[/(icon)](inline)'));
        $expected = '<p>' . $expected . '</p>';
        $this->assertEquals($expected, $text);
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

        $markdown = '/(plugin param)foo/(plugin param)';
        $expected = '<p><span>inline plugin</span>foo<span>inline plugin</span></p>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));

        $markdown = '/[text](plugin param)foo/[text](plugin param)';
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
        $parser = new HaikMarkdown();
        $parser->registerPluginRepository($repository);
    
        $markdown = '/(plugin)hr/(plugin)';
        $expected = '<p>/(plugin)hr/(plugin)</p>';
        
        $this->assertEquals($expected, trim($parser->transform($markdown)));
    }

    // ! convert plugin

    public function convertPluginTestProvider()
    {
        return array(
            'plugin_name_only' => array(
                'markdown' => ':::plugin:::',
                'expected' => '<div>convert plugin</div>',
            ),
            'plugin_name_only_wrapped_by_spaces' => array(
                'markdown' => '::: plugin :::',
                'expected' => '<div>convert plugin</div>',
            ),
            'plugin_name_only_with_many_colons' => array(
                'markdown' => ':::: plugin ::::',
                'expected' => '<div>convert plugin</div>',
            ),
            'plugin_name_only_with_differ_open_close_colons' => array(
                'markdown' => '::: plugin ::::',
                'expected' => '<p>::: plugin ::::</p>',
            ),
            'plugin_name_and_params' => array(
                'markdown' =>
                    ':::plugin'. "\n".
                    '---'. "\n".
                    '- param1'. "\n".
                    '- param2'. "\n".
                    ':::',
                'expected' => '<div>convert plugin</div>',
            ),
            'plugin_name_and_params_with_space' => array(
                'markdown' =>
                    '::: plugin'. "\n".
                    '---'. "\n".
                    '- param1'. "\n".
                    '- param2'. "\n".
                    ':::',
                'expected' => '<div>convert plugin</div>',
            ),
            'plugin_name_and_body' => array(
                'markdown' =>
                    ":::plugin\n".
                    "body\n".
                    ":::",
                'expected' => '<div>convert plugin</div>',
            ),
            'plugin_name_and_params_and_body' => array(
                'markdown' =>
                    ":::plugin\n".
                    "body\n".
                    "---\n".
                    "- param1\n".
                    "- param2\n".
                    ":::",
                'expected' => '<div>convert plugin</div>',
            ),
            'plugin_name_and_params_and_horizontal_lines' => array(
                'markdown' =>
                    ":::plugin\n".
                    "body\n".
                    "---\n".
                    "body\n".
                    "---\n".
                    "- param1\n".
                    "- param2\n".
                    ":::",
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

    public function testTakeLastHyphenLine()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('convert')
                 ->with(array('param1', 'param2'), "body\n---\nbody")
                 ->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $plugin_repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->andReturn($plugin_mock);
            return $mock;
        });
        $this->parser->registerPluginRepository($plugin_repository);

        $markdown = ":::plugin\n".
                    "body\n".
                    "---\n".
                    "body\n".
                    "---\n".
                    "- param1\n".
                    "- param2\n".
                    ":::";
        $result = $this->parser->transform($markdown);
        $expected = [
            'tag' => 'div',
            'content' => 'convert plugin'
        ];
        $this->assertTag($expected, $result);
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
        $plugin_repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->andReturn($plugin_mock);
            return $mock;
        });

        $markdown = ':::plugin(param1,param2)';
        $markdown = ":::plugin\n".
                    "---\n".
                    "- param1\n".
                    "- param2\n".
                    ":::";
        $expected = '<div>convert plugin</div>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }
    
    public function testCallConvertPluginWithBody()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('convert')
                 ->with(array(), "body\n")
                 ->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $plugin_repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->andReturn($plugin_mock);
            return $mock;
        });

        $markdown = ":::plugin\n".
                    "body\n".
                    ":::";
        $expected = '<div>convert plugin</div>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallConvertPluginWithParamsAndBody()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('convert')
                 ->with(array('param1', 'param2'), "body\n")
                 ->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $plugin_repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->andReturn($plugin_mock);
            return $mock;
        });

        $markdown = ":::plugin\n".
                    "body\n".
                    "---\n".
                    "- param1\n".
                    "- param2\n".
                    ":::";
        $expected = '<div>convert plugin</div>';

        $this->assertEquals($expected, trim($this->parser->transform($markdown)));
    }

    public function testCallConvertPluginWithYamlHashParamsAndBody()
    {
        $plugin_mock = Mockery::mock('Toiee\HaikMarkdown\Plugin\PluginInterface', function($mock)
        {
            $mock->shouldReceive('convert')
                 ->with(array('param1'=>'foo', 'param2'=>'bar'), "body\n")
                 ->andReturn('<div>convert plugin</div>');
            return $mock;
        });
        $plugin_repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->andReturn($plugin_mock);
            return $mock;
        });

        $markdown = ":::plugin\n".
                    "body\n".
                    "---\n".
                    "param1: foo\n".
                    "param2: bar\n".
                    ":::";
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
        $plugin_repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->andReturn($plugin_mock);
            return $mock;
        });

        $markdown = ":::plugin\n".
                    "body\n".
                    "---\n".
                    "- \"param,1\"\n".
                    "- \"param2,\"\n".
                    ":::";
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
        $plugin_repository = Mockery::mock('Toiee\HaikMarkdown\Plugin\Repositories\PluginRepositoryInterface', function($mock) use ($plugin_mock)
        {
            $mock->shouldReceive('exists')->andReturn(true);
            $mock->shouldReceive('load')->andReturn($plugin_mock);
            return $mock;
        });

        $markdown = ":::plugin:::\n".
                    ":::plugin:::";
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
        $parser = new HaikMarkdown();
        $parser->registerPluginRepository($repository);

        $markdown = ":::plugin\n".
                    "hoge\n".
                    ":::";
        $expected = "<p>:::plugin\nhoge\n:::</p>";
        
        $this->assertEquals($expected, trim($parser->transform($markdown)));
    }

}
