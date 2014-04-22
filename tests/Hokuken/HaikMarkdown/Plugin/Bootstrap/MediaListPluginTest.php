<?php
use Hokuken\HaikMarkdown\Plugin\Bootstrap\MediaList\MediaListPlugin;
use Hokuken\HaikMarkdown\HaikMarkdown;

class MediaListPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->plugin = new MediaListPlugin(new HaikMarkdown);
    }

    public function testConvertMethodExists()
    {
        $this->assertInternalType('string', $this->plugin->convert());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsExceptionWhenCallInline()
    {
        $this->plugin->inline();
    }

    /**
     * @dataProvider itemDataProvider
     */
    public function testStructureOfItemData($body, $expected)
    {
        $result = $this->plugin->convert(array(), $body);

        $this->assertAttributeEquals($expected, 'items', $this->plugin);
    }

    public function itemDataProvider()
    {
        $test = array(
            'empty' => array(
                'body' => '',
                'expected' => array()
            ),
            'empty_with_delimiter' => array(
                'body' => "====\n====\n",
                'expected' => array()
            ),
            'image_heading_body' => array(
                'body' => '![alt](http://placehold.jp/60x60.png)'."\n"
                        . '### Heading'."\n"
                        . 'Body',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'heading' => '<h3 class="media-heading">Heading</h3>',
                    'body' => '<p>Body</p>',
                ))
            ),
            'only_image' => array(
                'body' => '![alt](http://placehold.jp/60x60.png)',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'body' => '',
                ))
            ),
            'only_heading' => array(
                'body' => '### Heading',
                'expected' => array(array(
                    'heading' => '<h3 class="media-heading">Heading</h3>',
                    'body' => '',
                ))
            ),
            'only_body' => array(
                'body' => 'Body',
                'expected' => array(array(
                    'body' => '<p>Body</p>',
                ))
            ),
            'image_heading' => array(
                'body' => '![alt](http://placehold.jp/60x60.png)'."\n"
                        . '### Heading',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'heading' => '<h3 class="media-heading">Heading</h3>',
                    'body' => '',
                ))
            ),
            'image_body' => array(
                'body' => '![alt](http://placehold.jp/60x60.png)'."\n"
                        . 'Body',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'body' => '<p>Body</p>',
                ))
            ),
            'heading_body' => array(
                'body' => '### Heading'."\n"
                        . 'Body',
                'expected' => array(array(
                    'heading' => '<h3 class="media-heading">Heading</h3>',
                    'body' => '<p>Body</p>',
                ))
            ),
            'heading_body_image' => array(
                'body' => '### Heading'."\n"
                        . 'Body'."\n"
                        . '![alt](http://placehold.jp/60x60.png)',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'heading' => '<h3 class="media-heading">Heading</h3>',
                    'body' => '<p>Body</p>',
                    'align' => 'pull-right',
                ))
            ),
            'heading_image' => array(
                'body' => '### Heading'."\n"
                        . '![alt](http://placehold.jp/60x60.png)',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'heading' => '<h3 class="media-heading">Heading</h3>',
                    'body' => '',
                    'align' => 'pull-right',
                ))
            ),
            'body_image' => array(
                'body' => 'Body'."\n"
                        . '![alt](http://placehold.jp/60x60.png)',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'body' => '<p>Body</p>',
                    'align' => 'pull-right',
                ))
            ),
            'image_heading_body_image' => array(
                'body' => '![alt](http://placehold.jp/60x60.png)'."\n"
                        . '### Heading'."\n"
                        . 'Body'."\n"
                        . '![alt](http://placehold.jp/60x60.png)',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'heading' => '<h3 class="media-heading">Heading</h3>',
                    'body' => '<p>Body'."\n".'<img src="http://placehold.jp/60x60.png" alt="alt"></p>',
                ))
            ),
            'image_heading_image' => array(
                'body' => '![alt](http://placehold.jp/60x60.png)'."\n"
                        . '### Heading'."\n"
                        . '![alt](http://placehold.jp/60x60.png)',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'heading' => '<h3 class="media-heading">Heading</h3>',
                    'body' => '<p><img src="http://placehold.jp/60x60.png" alt="alt"></p>',
                ))
            ),
            'image_body_image' => array(
                'body' => '![alt](http://placehold.jp/60x60.png)'."\n"
                        . 'Body'."\n"
                        . '![alt](http://placehold.jp/60x60.png)',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'body' => '<p>Body'."\n".'<img src="http://placehold.jp/60x60.png" alt="alt"></p>',
                ))
            ),
            'image_image' => array(
                'body' => '![alt](http://placehold.jp/60x60.png)'."\n"
                        . '![alt](http://placehold.jp/60x60.png)',
                'expected' => array(array(
                    'image' => '<img class="media-object" src="http://placehold.jp/60x60.png" alt="alt">',
                    'body' => '<p><img src="http://placehold.jp/60x60.png" alt="alt"></p>',
                ))
            ),
            'heading_has_class' => array(
                'body' => '#### Heading {.custom-class}',
                'expected' => array(array(
                    'heading' => '<h4 class="custom-class">Heading</h4>',
                    'body' => '',
                ))
            ),
        );

        return $test;
    }

    public function testNoInput()
    {
        $params = array();
        $body = '';
        $result = $this->plugin->convert($params, $body);
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'haik-plugin-media-list'
            )
        );
        $this->assertTag($expected, $result);

        $not_expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'media'
            )
        );
        $this->assertNotTag($not_expected, $result);

        $not_expected = array(
            'tag' => 'img',
            'attributes' => array(
                'class' => 'media-object'
            )
        );
        $this->assertNotTag($not_expected, $result);

        $not_expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'media-body'
            )
        );
        $this->assertNotTag($not_expected, $result);

        $not_expected = array(
            'attributes' => array(
                'class' => 'media-heading'
            )
        );
        $this->assertNotTag($not_expected, $result);
    }

    public function testSetImage()
    {
        $params = array();
        $body = '![](sample.jpg)'."\n"
              . '#### Heading'."\n"
              . 'test';
        $result = $this->plugin->convert($params, $body);
        $expected = array(
            'tag' => 'img',
            'attributes' => array(
                'class' => 'media-object',
                'src' => 'sample.jpg',
                'alt' => ''
            ),
            'parent' => array(
                'tag' => 'span',
                'attributes' => array(
                    'class' => 'pull-left'
                )
            )
        );
        $this->assertTag($expected, $result);

        $expected = array(
            'tag' => 'h4',
            'attributes' => array(
                'class' => 'media-heading',
            ),
            'parent' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'media-body'
                )
            )
        );
        $this->assertTag($expected, $result);
    }

    public function testNoHeadingAndBody()
    {
        $params = array();
        $body = '![](sample.jpg)';
        $result = $this->plugin->convert($params, $body);
        $expected = array(
            'tag' => 'img',
            'attributes' => array(
                'class' => 'media-object',
                'src' => 'sample.jpg',
                'alt' => ''
            ),
            'parent' => array(
                'tag' => 'span',
                'attributes' => array(
                    'class' => 'pull-left'
                )
            )
        );
        $this->assertTag($expected, $result);

        $not_expected = array(
            'tag' => 'h4',
            'attributes' => array(
                'class' => 'media-heading',
            ),
            'parent' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'media-body'
                )
            )
        );
        $this->assertNotTag($not_expected, $result);
    }

    public function testOnlyHeading()
    {
        $params = array();
        $body = '#### Heading';
        $result = $this->plugin->convert($params, $body);
        $expected = array(
            'tag' => 'h4',
            'attributes' => array(
                'class' => 'media-heading'
            ),
            'parent' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'media-body'
                )
            )
        );
        $this->assertTag($expected, $result);

        $expected = array(
            'tag' => 'img',
            'attributes' => array(
                'class' => 'media-object',
                'src' => 'http://placehold.jp/80x80.png',
                'alt' => ''
            ),
            'parent' => array(
                'tag' => 'span',
                'attributes' => array(
                    'class' => 'pull-left'
                )
            )
        );
        $this->assertTag($expected, $result);

        $not_expected = array(
            'tag' => 'div',
            'attributes' => array('class' => 'media-body'),
            'child' => array(
                'tag' => 'p'
            )
        );
        $this->assertNotTag($not_expected, $result);
    }

    public function testOnlyBody()
    {
        $params = array();
        $body = 'test';
        $result = $this->plugin->convert($params, $body);

        $expected = array(
            'tag' => 'p',
            'parent' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'media-body'
                )
            )
        );
        $this->assertTag($expected, $result);

        $expected = array(
            'tag' => 'img',
            'attributes' => array(
                'class' => 'media-object',
                'src' => 'http://placehold.jp/80x80.png',
                'alt' => ''
            ),
            'parent' => array(
                'tag' => 'span',
                'attributes' => array(
                    'class' => 'pull-left'
                )
            )
        );
        $this->assertTag($expected, $result);

        $not_expected = array(
            'attributes' => array('class' => 'media-heading'),
            'parent' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'media-body'
                )
            )
        );
        $this->assertNotTag($not_expected, $result);
    }

    public function testSetHeadingAndBody()
    {
        $params = array();
        $body = '#### Heading'."\n"
              . 'Body';
        $result = $this->plugin->convert($params, $body);

        $expected = array(
            'tag' => 'p',
            'parent' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'media-body'
                )
            )
        );
        $this->assertTag($expected, $result);

        $expected = array(
            'tag' => 'img',
            'attributes' => array(
                'class' => 'media-object',
                'src' => 'http://placehold.jp/80x80.png',
                'alt' => ''
            ),
            'parent' => array(
                'tag' => 'span',
                'attributes' => array(
                    'class' => 'pull-left'
                )
            )
        );
        $this->assertTag($expected, $result);

        $expected = array(
            'attributes' => array('class' => 'media-heading'),
            'parent' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'media-body'
                )
            )
        );
        $this->assertTag($expected, $result);
    }

    public function testAlignIsLeft()
    {
        $params = array();
        $body = '![](sample-left.jpg)'."\n"
              . '#### Heading'."\n"
              . 'Body'."\n"
              . '![](sample.jpg)';
        $result = $this->plugin->convert($params, $body);

        $expected = array(
            'tag' => 'img',
            'attributes' => array(
                'class' => 'media-object',
                'src' => 'sample-left.jpg',
                'alt' => ''
            ),
            'parent' => array(
                'tag' => 'span',
                'attributes' => array(
                    'class' => 'pull-left'
                )
            )
        );
        $this->assertTag($expected, $result);
    }

    public function testAlignIsRight()
    {
        $params = array();
        $body = '#### Heading'."\n"
              . 'Body'."\n"
              . '![](sample-right.jpg)';
        $result = $this->plugin->convert($params, $body);

        $expected = array(
            'tag' => 'img',
            'attributes' => array(
                'class' => 'media-object',
                'src' => 'sample-right.jpg',
                'alt' => ''
            ),
            'parent' => array(
                'tag' => 'span',
                'attributes' => array(
                    'class' => 'pull-right'
                )
            )
        );
        $this->assertTag($expected, $result);
    }

    public function testOneMediaListWithMarkdownImage()
    {
        $this->markTestIncomplete();

        # This is the test of left image, heading, body set.
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<h4 class="media-heading">test title</h4>'."\n"
                      . '<p>test</p>'."\n"
                      . '</div></div>',
        );

        $body = "![alt](http://placehold.jp/80x80.png)\n"
              . "#### test title\n"
              . "test\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);


        # This is the test of left DUMMY image, heading, body set.
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<h4 class="media-heading">test title</h4>'."\n"
                      . '<p>test</p>'."\n"
                      . '</div></div>'."\n",
        );

        $body = "#### test title\n"
              . "test\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);



        # This is the test of left DUMMY image and body set.
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<p>test</p>'."\n"
                      . '</div></div>',
        );

        $body = "test\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);



        # This is the test of left image, body set.
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'
                      . '<span class="pull-left">'
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'
                      . '</span>'
                      . '<div class="media-body">'
                      . '<p>test</p>'
                      . '</div></div>',
        );

        $body = "![alt](http://placehold.jp/80x80.png)\n"
              . "test\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);



        # This is the test of right image, heading, body set.
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-right">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<h4 class="media-heading">test title</h4>'."\n"
                      . '<p>test</p>'."\n"
                      . '</div></div>',
        );

        $body = "#### test title\n"
              ."test\n"
              . "![alt](http://placehold.jp/80x80.png)\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);


        # This is the test of left image, heading, body set with many breaks between each.
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<h4 class="media-heading">test title</h4>'."\n"
                      . '<p>test</p>'."\n"
                      . '</div></div>',
        );

        $body = "\n\n\n"
              . "![alt](http://placehold.jp/80x80.png)\n"
              . "\n\n"
              . "#### test title\n"
              . "\n\n\n"
              . "test\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);


        # This is the test of left image, heading, some lines body set.
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<h4 class="media-heading">test title</h4>'."\n"
                      . "<p>test\ntest\ntest</p>"."\n"
                      . '</div></div>',
        );

        $body = "![alt](http://placehold.jp/80x80.png)\n"
              . "#### test title\n"
              . "test\n"
              . "test\n"
              . "test\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);


        # This is the test of left image, heading, some lines body with break set.
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<h4 class="media-heading">test title</h4>'."\n"
                      . "<p>test<br>\ntest\ntest</p>"."\n"
                      . '</div></div>',
        );

        $body = "![alt](http://placehold.jp/80x80.png)\n"
              . "#### test title\n"
              . "test  \n"
              . "test\n"
              . "test\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);


        # This is the test of medialists body contains same <img> of img.media-object
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<h4 class="media-heading">test title</h4>'."\n"
                      . '<p>test'."\n".'<img src="http://placehold.jp/80x80.png" alt="alt"></p>'."\n"
                      . '</div></div>',
        );

        $body = "![alt](http://placehold.jp/80x80.png)\n"
              . "#### test title\n"
              . "test\n"
              . "![alt](http://placehold.jp/80x80.png)\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);
        
        # This is the test of medialists only body contains heading
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<p>test</p>'."\n"
                      . '<h4>test title</h4>'."\n"
                      . '</div></div>',
        );

        $body = "![alt](http://placehold.jp/80x80.png)\n"
              . "test\n"
              . "#### test title\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);
        
        # This is the test of medialists order by heading heading body
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<h4 class="media-heading">test title</h4>'."\n"
                      . '<h4>test title</h4>'."\n"
                      . '<p>test</p>'."\n"
                      . '</div></div>',
        );

        $body = "#### test title\n"
              . "#### test title\n"
              . "test\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);

        # This is the test of medialists order by heading heading image
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-right">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<h4 class="media-heading">test title</h4>'."\n"
                      . '<h4>test title</h4>'."\n"
                      . '</div></div>',
        );

        $body = "#### test title\n"
              . "#### test title\n"
              . "![alt](http://placehold.jp/80x80.png)\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);

        # This is the test of medialists order by heading image heading
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<h4 class="media-heading">test title</h4>'."\n"
                      . '<p><img src="http://placehold.jp/80x80.png" alt="alt"></p>'."\n"
                      . '<h4>test title</h4>'."\n"
                      . '</div></div>',
        );

        $body = "#### test title\n"
              . "![alt](http://placehold.jp/80x80.png)\n"
              . "#### test title\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);

        # This is the test of medialists order by body image heading
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<p>test'."\n"
                      . '<img src="http://placehold.jp/80x80.png" alt="alt"></p>'."\n"
                      . '<h4>test title</h4>'."\n"
                      . '</div></div>',
        );

        $body = "test\n"
              . "![alt](http://placehold.jp/80x80.png)\n"
              . "#### test title\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);

        # This is the test of medialists order by body head image
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-right">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<p>test</p>'."\n"
                      . '<h4>test title</h4>'."\n"
                      . '</div></div>',
        );

        $body = "test\n"
              . "#### test title\n"
              . "![alt](http://placehold.jp/80x80.png)\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);

        # This is the test of medialists order by image body image
        $test = array(
            'medialist' => array(),
            'assert' => '<div class="haik-plugin-medialist media">'."\n"
                      . '<span class="pull-left">'."\n"
                      . '<img class="media-object" src="http://placehold.jp/80x80.png" alt="alt">'."\n"
                      . '</span>'."\n"
                      . '<div class="media-body">'."\n"
                      . '<p>test'."\n"
                      . '<img src="http://placehold.jp/80x80.png" alt="alt"></p>'."\n"
                      . '</div></div>',
        );

        $body = "![alt](http://placehold.jp/80x80.png)\n"
              . "test\n"
              . "![alt](http://placehold.jp/80x80.png)\n";

        $expect_return = preg_replace('/\n| {2,}/', '', trim($test['assert']));
        $actual_return = with(new MediaListPlugin)->convert($test['medialist'], $body);
        $actual_return = preg_replace('/\n| {2,}/', '', trim($actual_return));

        $this->assertEquals($expect_return, $actual_return);
    }

    public function testSetItemsWithBodyHasMultiMediaList()
    {
        $params = array();
        $body = '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test' . "\n"
              . '====' . "\n"
              . '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test';
        $this->plugin->convert($params, $body);
        $expected = 2;

        $this->assertAttributeCount($expected, 'items', $this->plugin);
    }

    public function testWrapBootstrapRowWithColumnOption()
    {
        $params = array('6');
        $body = '';
        $result = $this->plugin->convert($params, $body);
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'row'
            ),
            'child' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'col-sm-6',
                ),
                'child' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'haik-plugin-media-list'
                    ),
                ),
            )
        );
        $this->assertTag($expected, $result);
    }

    public function testWrapBootstrapRowWithHashColumnOption()
    {
        $params = array('span' => '6');
        $body = '';
        $result = $this->plugin->convert($params, $body);
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'row'
            ),
            'child' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'col-sm-6',
                ),
                'child' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'haik-plugin-media-list'
                    ),
                ),
            )
        );
        $this->assertTag($expected, $result);
    }

}
