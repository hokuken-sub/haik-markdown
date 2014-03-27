<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Thumbnails\ThumbnailsPlugin;
use Michelf\MarkdownExtra;

class ThumbnailsPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('<p>test</p>');
            return $mock;
        });
        $this->plugin = new ThumbnailsPlugin($this->parser);
    }

    public function testConvertMethodExists()
    {
        $this->assertInternalType('string',$this->plugin->convert());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsExceptionWhenCallInline()
    {
        $this->plugin->inline();
    }

    public function testInstanceOfColsPlugin()
    {
        $this->assertInstanceOf('\Toiee\HaikMarkdown\Plugin\Bootstrap\Cols\ColsPlugin', $this->plugin);
    }

    /**
     * @dataProvider bodyProvider
     */
    public function testHtml($body, $expected)
    {
        $parser = Mockery::mock('\Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')
                 ->once()->andReturn('<img src="sample.jpg" alt="sample" title="sample">');
            $mock->shouldReceive('transform')
                 ->twice()->andReturn('<p>test</p>');
            return $mock;
        });
        $this->plugin = new ThumbnailsPlugin($parser);
        $result = $this->plugin->convert(array(), $body);
        $this->assertTag($expected, $result);
    }

    public function bodyProvider()
    {
        return array(
            'no_params' => array(
                'body' => 'test',
                'expected' => array(
                    'ancestor' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'haik-plugin-thumbnails row'
                        ),
                    ),
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'thumbnail',
                    ),
                    'child' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'caption'
                        )
                    ),
                ),
            ),
            'one' => array(
                'body' => '![sample](sample.jpg "sample")'."\n".
                          "#thumbnail title\n".
                          "body1\n".
                          "body2",
                'expected' => array(
                    'ancestor' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'haik-plugin-thumbnails row'
                        ),
                    ),
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'thumbnail',
                    ),
                    'child' => array(
                        'tag' => 'img',
                        'attributes' => array(
                            'src' => 'sample.jpg',
                            'alt' => 'sample',
                            'title' => 'sample',
                        )
                    ),
                    'descendant' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'caption',
                        ),
                    )
                )
            ),
        );
    }

}
