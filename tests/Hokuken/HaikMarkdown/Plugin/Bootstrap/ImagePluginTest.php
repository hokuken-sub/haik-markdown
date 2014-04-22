<?php
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Image\ImagePlugin;

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
                'image' => array('http://placehold.jp/200x200.png', ['class'=>'custom']),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_circle_custom' => array(
                'image' => array('http://placehold.jp/200x200.png', 'circle', ['class'=>'custom']),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
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
            'custom_circle' => array(
                'image' => array(['class'=>'custom'], 'circle'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-circle custom',
                        'src'   => 'http://placehold.jp/300x300.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'class is hash' => array(
                'image' => array('http://placehold.jp/200x200.png', ['class'=>'custom']),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            // hash params
            '#url' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#path' => array(
                'image' => array('path' => 'http://placehold.jp/200x200.png'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#image' => array(
                'image' => array('image' => 'http://placehold.jp/200x200.png'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#type:rounded' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'type' => 'rounded'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-rounded',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#class' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'class' => 'custom'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#type:circle,#class:custom' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'type' => 'circle', 'class' => 'custom'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#class#3' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'class' => 'custom custom2'),
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
                'image' => array('http://placehold.jp/200x200.png', ['class'=>'custom']),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_title' => array(
                'image' => array('http://placehold.jp/200x200.png', 'title text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title text',
                    ),
                ),
            ),
            'image_circle_custom' => array(
                'image' => array('http://placehold.jp/200x200.png', 'circle', ['class'=>'custom']),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            'image_thumbnail_title' => array(
                'image' => array('http://placehold.jp/200x200.png', 'thumbnail', 'title text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-thumbnail',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title text',
                    ),
                ),
            ),
            'image_custom_title' => array(
                'image' => array('http://placehold.jp/200x200.png', ['class'=>'custom'], 'title text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title text',
                    ),
                ),
            ),
            'image_circle_custom_title' => array(
                'image' => array('http://placehold.jp/200x200.png', 'circle', ['class'=>'custom'], 'title text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title text',
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
            'circle_custom_image_title' => array(
                'image' => array('circle', ['class'=>'custom'], 'http://placehold.jp/200x200.png', 'title text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title text',
                    ),
                ),
            ),
            'custom_circle' => array(
                'image' => array(['class'=>'custom'], 'circle'),
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
                'image' => array('http://placehold.jp/200x200.png', 'circle', ['class'=>'custom'], 'title text', 'too_many'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title text too_many',
                    ),
                ),
            ),
            'image_multi_custom' => array(
                'image' => array('http://placehold.jp/200x200.png', ['class'=>'custom custom2']),
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
                'image' => array('http://placehold.jp/200x200.png', 'rounded', ['class'=>'custom'], 'left'),
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
                'image' => array('http://placehold.jp/200x200.png', 'right', ['class'=>'custom']),
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
            'image_rounded_custom_pull_left_title' => array(
                'image' => array('http://placehold.jp/200x200.png', 'rounded', ['class'=>'custom'], 'left', 'title-text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-rounded custom pull-left',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title-text',
                        'style' => 'margin: 0 15px 15px 0;',
                    ),
                ),
            ),
            // hash params
            '#url' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#type' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'type' => 'rounded'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-rounded',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#class' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'class' => 'custom'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#title' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'title' => 'title text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title text',
                    ),
                ),
            ),
            '#type:circle, #class:custom' => array(
                'image' => array('http://placehold.jp/200x200.png', 'type' => 'circle', 'class' => 'custom'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#type:thumbnail,#title' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'type' => 'thumbnail', 'title' => 'title title'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-thumbnail',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title title',
                    ),
                ),
            ),
            '#class, #title' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'class' => 'custom', 'title' => 'title text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title text',
                    ),
                ),
            ),
            '#type, #class, #title' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'type' => 'circle', 'class' => 'custom', 'title' => 'title text'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive img-circle custom',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'title'   => 'title text',
                    ),
                ),
            ),
            '#class #2' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'class' => 'custom custom2'),
                'expected' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'class' => 'haik-plugin-image img-responsive custom custom2',
                        'src'   => 'http://placehold.jp/200x200.png',
                        'alt'   => '',
                    ),
                ),
            ),
            '#pull' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'pull' => 'left'),
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
            '#align' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'align' => 'left'),
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
            '#pull:right' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'pull' => 'right'),
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
            '#type, #pull' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'type' => 'rounded', 'pull' => 'left'),
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
            '#type, #class, #pull' => array(
                'image' => array('url' => 'http://placehold.jp/200x200.png', 'type' => 'rounded', 'class' => 'custom', 'pull' => 'left'),
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
        );
    }

    public function bodyProvider()
    {
        return [
            [
                'alt text',
                [
                    'tag' => 'img',
                    'attributes' => [
                        'alt' => 'alt text',
                        'title' => 'alt text',
                    ]
                ],
                'alt <span>text</span>',
                [
                    'tag' => 'img',
                    'attributes' => [
                        'alt' => 'alt text',
                        'title' => 'alt text',
                    ]
                ],
                '<img src="sample.jpg" alt="alt text">',
                [
                    'tag' => 'img',
                    'attributes' => [
                        'alt' => '',
                        'title' => '',
                    ]
                ],
                '>here',
                [
                    'tag' => 'img',
                    'attributes' => [
                        'alt' => '&gt;here',
                        'title' => '&gt;here'
                    ]
                ],
            ]
        ];
    }

    /**
     * @dataProvider bodyProvider
     */
    public function testBody($body, $expected)
    {
        $params = ['url' => 'sample.jpg'];
        $result = $this->plugin->inline($params, $body);
        $this->assertTag($expected, $result);
    }

    public function testAltAndTitle()
    {
        $params = ['url' => 'sample.jpg', 'title' => 'title text'];
        $body = 'alt text';
        $expected = [
            'tag' => 'img',
            'attributes' => [
                'alt' => 'alt text',
                'title' => 'title text'
            ]
        ];
        $result = $this->plugin->inline($params, $body);
        $this->assertTag($expected, $result);
    }
}
