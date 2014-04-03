<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Image\ImagePlugin;

class ImagePluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface');
        $this->plugin = new ImagePlugin($this->parser);
    }

    public function testInlineMethodExists()
    {
        $this->assertInternalType('string', $this->plugin->inline());
    }

    public function testConvertMethodExists()
    {
        $this->assertInternalType('string', $this->plugin->convert());
    }

    /**
     *  @dataProvider paramsProviderForInline
     */
    public function testParameterWhenMethodIsInline($params, $expected)
    {
        $result = $this->plugin->inline($params);
        $this->assertTag($expected, $result);
    }

    public function paramsProviderForInline()
    {
        return array(
            'none' => array(
                'image' => array(),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image',
                        'src'   => 'http://placehold.jp/300x300.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'only_image' => array(
                'image' => array('http://placehold.jp/200x200.png'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_rounded' => array(
                'image' => array('http://placehold.jp/200x200.png', 'rounded'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-rounded',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_custom' => array(
                'image' => array('http://placehold.jp/200x200.png', 'class=custom'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_alt' => array(
                'image' => array('http://placehold.jp/200x200.png', 'alt_text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text',
                    ),
                ),
            ),
            'image_circle_custom' => array(
                'image' => array('http://placehold.jp/200x200.png', 'circle', 'class=custom'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_thumbnail_alt' => array(
                'image' => array('http://placehold.jp/200x200.png', 'thumbnail', 'alt_text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-thumbnail',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text',
                    ),
                ),
            ),
            'image_custom_alt' => array(
                'image' => array('http://placehold.jp/200x200.png', 'class=custom', 'alt_text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text',
                    ),
                ),
            ),
            'image_circle_custom_alt' => array(
                'image' => array('http://placehold.jp/200x200.png', 'circle', 'class=custom', 'alt_text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text',
                    ),
                ),
            ),
            'multi_types' => array(
                'image' => array('circle', 'thumbnail'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-circle',
                        'src'   => 'http://placehold.jp/300x300.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'circle_custom_image_alt' => array(
                'image' => array('circle', 'class=custom', 'http://placehold.jp/200x200.png', 'alt_text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text',
                    ),
                ),
            ),
            'custom_circle' => array(
                'image' => array('class=custom', 'circle'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-circle custom',
                        'src'   => 'http://placehold.jp/300x300.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'too_many_params' => array(
                'image' => array('http://placehold.jp/200x200.png', 'circle', 'class=custom', 'alt_text', 'too_many'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text too_many',
                    ),
                ),
            ),
            'image_multi_custom' => array(
                'image' => array('http://placehold.jp/200x200.png', 'class=custom custom2'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image custom custom2',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
        );
    }

    /**
     *  @dataProvider paramsProviderForConvert
     */
    public function testParameterWhenMethodIsConvert($params, $expected)
    {
        $result = $this->plugin->convert($params);
        $this->assertTag($expected, $result);
    }

    public function paramsProviderForConvert()
    {
        return array(
            'none' => array(
                'image' => array(),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive',
                        'src'   => 'http://placehold.jp/300x300.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'only_image' => array(
                'image' => array('http://placehold.jp/200x200.png'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_rounded' => array(
                'image' => array('http://placehold.jp/200x200.png', 'rounded'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-rounded',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_custom' => array(
                'image' => array('http://placehold.jp/200x200.png', 'class=custom'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_alt' => array(
                'image' => array('http://placehold.jp/200x200.png', 'alt_text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text',
                    ),
                ),
            ),
            'image_circle_custom' => array(
                'image' => array('http://placehold.jp/200x200.png', 'circle', 'class=custom'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_thumbnail_alt' => array(
                'image' => array('http://placehold.jp/200x200.png', 'thumbnail', 'alt_text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-thumbnail',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text',
                    ),
                ),
            ),
            'image_custom_alt' => array(
                'image' => array('http://placehold.jp/200x200.png', 'class=custom', 'alt_text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text',
                    ),
                ),
            ),
            'image_circle_custom_alt' => array(
                'image' => array('http://placehold.jp/200x200.png', 'circle', 'class=custom', 'alt_text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text',
                    ),
                ),
            ),
            'multi_types' => array(
                'image' => array('circle', 'thumbnail'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle',
                        'src'   => 'http://placehold.jp/300x300.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'circle_custom_image_alt' => array(
                'image' => array('circle', 'class=custom', 'http://placehold.jp/200x200.png', 'alt_text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text',
                    ),
                ),
            ),
            'custom_circle' => array(
                'image' => array('class=custom', 'circle'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/300x300.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'too_many_params' => array(
                'image' => array('http://placehold.jp/200x200.png', 'circle', 'class=custom', 'alt_text', 'too_many'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt_text too_many',
                    ),
                ),
            ),
            'image_multi_custom' => array(
                'image' => array('http://placehold.jp/200x200.png', 'class=custom custom2'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive custom custom2',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_pull_left' => array(
                'image' => array('http://placehold.jp/200x200.png', 'left'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive pull-left',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                        'style' => 'margin: 0 15px 15px 0;',
                    ),
                ),
            ),
            'image_pull_right' => array(
                'image' => array('http://placehold.jp/200x200.png', 'right'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive pull-right',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                        'style' => 'margin: 0 0 15px 15px;',
                    ),
                ),
            ),
            'image_rounded_pull_left' => array(
                'image' => array('http://placehold.jp/200x200.png', 'rounded', 'left'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-rounded pull-left',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                        'style' => 'margin: 0 15px 15px 0;',
                    ),
                ),
            ),
            'image_rounded_custom_pull_left' => array(
                'image' => array('http://placehold.jp/200x200.png', 'rounded', 'class=custom', 'left'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-rounded custom pull-left',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                        'style' => 'margin: 0 15px 15px 0;',
                    ),
                ),
            ),
            'image_pull_right_custom' => array(
                'image' => array('http://placehold.jp/200x200.png', 'right', 'class=custom'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive custom pull-right',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                        'style' => 'margin: 0 0 15px 15px;',
                    ),
                ),
            ),
            'image_rounded_custom_pull_left_alt' => array(
                'image' => array('http://placehold.jp/200x200.png', 'rounded', 'class=custom', 'left', 'alt-text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-rounded custom pull-left',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => 'alt-text',
                        'style' => 'margin: 0 15px 15px 0;',
                    ),
                ),
            ),
        );
    }
}
