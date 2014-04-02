<?php
use Toiee\HaikMarkdown\Plugin\FlatUI\Tile\TilePlugin;
use Toiee\HaikMarkdown\HaikMarkdown;

class TilePluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->plugin = new TilePlugin(new HaikMarkdown);
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
    public function testHtml($params, $body, $expected)
    {
        $result = $this->plugin->convert($params, $body);
        $this->assertTag($expected, $result);
    }

    public function bodyProvider()
    {
        return array(
            'no_params' => array(
                'params' => array(),
                'body' => 'test',
                'expected' => array(
                    'ancestor' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'haik-plugin-tile row'
                        ),
                    ),
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'tile',
                    ),
                    'child' => array(
                        'tag' => 'p',
                        'content' => 'test'
                    ),
                ),
            ),
            'one' => array(
                'params' => array(),
                'body' => '![sample](sample.jpg "sample")'."\n".
                          "#thumbnail title\n".
                          "body1\n".
                          "body2",
                'expected' => array(
                    'ancestor' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'haik-plugin-tile row'
                        ),
                    ),
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'tile',
                    ),
                    'child' => array(
                        'tag' => 'img',
                        'attributes' => array(
                            'src' => 'sample.jpg',
                            'alt' => 'sample',
                            'title' => 'sample',
                            'class' => 'tile-image big-illustration'
                        )
                    ),
                    'descendant' => array(
                        'tag' => 'h1',
                        'attributes' => array(
                            'class' => 'tile-title',
                        ),
                    )
                )
            ),
            'two' => array(
                'params' => array("6","6"),
                'body'   => '![sample](sample.jpg "sample")'."\n".
                          "##thumbnail title\n".
                          "body1\n".
                          "body2\n".
                          "\n====\n".
                          '![sample](sample.jpg "sample")'."\n".
                          "#thumbnail2 title\n".
                          "body3\n".
                          "body4",
                'expected' => array(
                    'ancestor' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'col-sm-6'
                        ),
                    ),
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'tile',
                    ),
                    'child' => array(
                        'tag' => 'img',
                        'attributes' => array(
                            'src' => 'sample.jpg',
                            'alt' => 'sample',
                            'title' => 'sample',
                            'class' => 'tile-image big-illustration'
                        )
                    ),
                    'descendant' => array(
                        'tag' => 'h2',
                        'attributes' => array(
                            'class' => 'tile-title',
                        ),
                    )
                )
            ),
            'hot' => array(
                'params' => array("3.hot","4"),
                'body'   => '![sample](sample.jpg "sample")'."\n".
                          "##thumbnail title\n".
                          "body1\n".
                          "body2\n".
                          "\n====\n".
                          '![sample](sample.jpg "sample")'."\n".
                          "#thumbnail2 title\n".
                          "body3\n".
                          "body4",
                'expected' => array(
                    'ancestor' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'col-sm-3'
                        ),
                    ),
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'tile tile-hot',
                    ),
                    'child' => array(
                        'tag' => 'img',
                        'attributes' => array(
                            'src' => 'sample.jpg',
                            'alt' => 'sample',
                            'title' => 'sample',
                            'class' => 'tile-image big-illustration'
                        )
                    ),
                    'descendant' => array(
                        'tag' => 'h2',
                        'attributes' => array(
                            'class' => 'tile-title',
                        ),
                    )
                )
            ),
            'popular' => array(
                'params' => array("5.popular","7"),
                'body'   => '![sample](sample.jpg "sample")'."\n".
                          "##thumbnail title\n".
                          "body1\n".
                          "body2\n".
                          "\n====\n".
                          '![sample](sample.jpg "sample")'."\n".
                          "#thumbnail2 title\n".
                          "body3\n".
                          "body4",
                'expected' => array(
                    'ancestor' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'col-sm-5'
                        ),
                    ),
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'tile tile-hot',
                    ),
                    'child' => array(
                        'tag' => 'img',
                        'attributes' => array(
                            'src' => 'sample.jpg',
                            'alt' => 'sample',
                            'title' => 'sample',
                            'class' => 'tile-image big-illustration'
                        )
                    ),
                    'descendant' => array(
                        'tag' => 'h2',
                        'attributes' => array(
                            'class' => 'tile-title',
                        ),
                    )
                )
            ),
            'tile-hot' => array(
                'params' => array("6.popular","6"),
                'body'   => '![sample](sample.jpg "sample")'."\n".
                          "##thumbnail title\n".
                          "body1\n".
                          "body2\n".
                          "\n====\n".
                          '![sample](sample.jpg "sample")'."\n".
                          "#thumbnail2 title\n".
                          "body3\n".
                          "body4",
                'expected' => array(
                    'ancestor' => array(
                        'tag' => 'div',
                        'attributes' => array(
                            'class' => 'col-sm-6'
                        ),
                    ),
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'tile tile-hot',
                    ),
                    'child' => array(
                        'tag' => 'img',
                        'attributes' => array(
                            'src' => 'sample.jpg',
                            'alt' => 'sample',
                            'title' => 'sample',
                            'class' => 'tile-image big-illustration'
                        )
                    ),
                    'descendant' => array(
                        'tag' => 'h2',
                        'attributes' => array(
                            'class' => 'tile-title',
                        ),
                    )
                )
            ),        );
    }

}
