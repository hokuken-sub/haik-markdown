<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Carousel\CarouselPlugin;
use Toiee\HaikMarkdown\HaikMarkdown;

class CarouselPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('<p>test</p>');
            return $mock;
        });
        $this->plugin = new CarouselPlugin($this->parser);
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

    public function testId()
    {
        $id = $this->plugin->getId();
        $result = $this->plugin->convert();
        $expected = array(
            'id' => 'haik_plugin_carousel_' . $id,
            'tag' => 'div',
            'attributes' => array(
                'class' => 'haik-plugin-carousel carousel slide',
                'data-ride' => 'carousel',
            ),
        );
        $this->assertTag($expected, $result);
    }

    public function testSetOptionsWithSingleSlide()
    {
        $params = array();
        $body = '![alt](http://placehold.jp/1000x400.png)' . "\n"
              . '### Heading' . "\n"
              . 'Body';
        $expected = array(
            'indicatorsSet' => false,
            'controlsSet'   => false,
        );
        $this->plugin->convert($params, $body);

        $this->assertAttributeEquals($expected, 'options', $this->plugin);
    }

    public function testSetOptionsWithNoButtonOption()
    {
        $params = array('nobutton');
        $body = 'Body' . "\n"
              . '====' . "\n"
              . 'Body';
        $expected = array(
            'indicatorsSet' => false,
            'controlsSet'   => false,
        );
        $this->plugin->convert($params, $body);

        $this->assertAttributeEquals($expected, 'options', $this->plugin);
    }

    public function testSetOptionsWithNoIndicatorOption()
    {
        $params = array('noindicator');
        $body = 'Body' . "\n"
              . '====' . "\n"
              . 'Body';
        $expected = array(
            'indicatorsSet' => false,
            'controlsSet'   => true,
        );
        $this->plugin->convert($params, $body);

        $this->assertAttributeEquals($expected, 'options', $this->plugin);
    }

    public function testSetOptionsWithNoControlsOption()
    {
        $params = array('noslidebutton');
        $body = 'Body' . "\n"
              . '====' . "\n"
              . 'Body';
        $expected = array(
            'indicatorsSet' => true,
            'controlsSet'   => false,
        );
        $this->plugin->convert($params, $body);

        $this->assertAttributeEquals($expected, 'options', $this->plugin);
    }

    public function testRenderFullStack()
    {
        $this->plugin = new CarouselPlugin(new HaikMarkdown);

        $body = "![](sample.jpg)\n"
              . "#### test title\n"
              . "test\n";
        $expected = array(
            'tag' => 'img',
            'attributes' => array(
                'src' => 'sample.jpg',
                'alt' => ''
            ),
            'parent' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'item active',
                )
            ),
        );
        $result = $this->plugin->convert(array(), $body);

        $this->assertTag($expected, $result);        
    }

    public function testNoImage()
    {
        $this->plugin = new CarouselPlugin(new HaikMarkdown);

        $class_name = get_class($this->plugin);
        $src = 'http://placehold.jp/900x500.png';

        $body = "#### test title\n"
              . "test\n";
        $expected = array(
            'tag' => 'img',
            'attributes' => array(
                'src' => $src,
                'alt' => ''
            ),
            'parent' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'item active',
                )
            ),
        );
        $result = $this->plugin->convert(array(), $body);

        $this->assertTag($expected, $result);
    }

    public function testNoHeadingAndBody()
    {
        $this->plugin = new CarouselPlugin(new HaikMarkdown);
        $params = array();
        $body = "![alt](http://placehold.jp/1000x400.png)";
        $not_expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'item active',
            ),
            'child' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'carousel-caption'
                )
            )
        );
        $result = $this->plugin->convert($params, $body);

        $this->assertNotTag($not_expected, $result);
    }

    public function testSetOnlyHeading()
    {
        $this->plugin = new CarouselPlugin(new HaikMarkdown);
        $params = array();
        $body = '### Heading';
        $not_expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'carousel-caption',
            ),
            'child' => array(
                'tag' => 'p'
            )
        );
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'carousel-caption',
            ),
            'child' => array(
                'tag' => 'h3'
            )
        );

        $result = $this->plugin->convert($params, $body);
        $this->assertTag($expected, $result);
        $this->assertNotTag($not_expected, $result);
    }

    public function testSetOnlyBody()
    {
        $this->plugin = new CarouselPlugin(new HaikMarkdown);
        $params = array();
        $body = 'test';
        $not_expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'carousel-caption',
            ),
            'child' => array(
                'tag' => 'h3'
            )
        );
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'carousel-caption',
            ),
            'child' => array(
                'tag' => 'p'
            )
        );
        $result = $this->plugin->convert($params, $body);
        $this->assertTag($expected, $result);
        $this->assertNotTag($not_expected, $result);
    }

    public function testSetHeadingAndBody()
    {
        $this->plugin = new CarouselPlugin(new HaikMarkdown);
        $params = array();
        $body = "### Heading\n"
              . "test\n";
        $expects = array(
            array(
                'tag' => 'img',
                'parent' => array(
                    'tag' => 'div',
                    'attributes' => array(
                        'class' => 'item active'
                    )
                )
            ),
            array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'carousel-caption',
                ),
                'child' => array(
                    'tag' => 'h3'
                )
            ),
            array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'carousel-caption',
                ),
                'child' => array(
                    'tag' => 'p'
                )
            ),
        );

        $result = $this->plugin->convert($params, $body);
        foreach ($expects as $expected)
        {
            $this->assertTag($expected, $result);
        }
    }

    public function setUpForTestSetItems()
    {
        $this->plugin = new CarouselPlugin(new HaikMarkdown);
    }

    public function testSetEmptyItemsWithEmptyBody()
    {
        $this->setUpForTestSetItems();

        $body = '';
        $expected = array();
        $result = $this->plugin->convert(array(), $body);
        
        $this->assertAttributeEquals($expected, 'items', $this->plugin);
    }

    public function testSetEmptyItemsWithEmptyBodies()
    {
        $this->setUpForTestSetItems();

        $body = "====\n====\n";
        $expected = array();
        $result = $this->plugin->convert(array(), $body);

        $this->assertAttributeEquals($expected, 'items', $this->plugin);
    }

    public function testSetItemsWithFullStackBody()
    {
        $this->setUpForTestSetItems();

        $body = '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test';
        $expected = array(array(
            'image' => '<img src="sample.jpg" alt="">',
            'heading' => '<h3>Heading</h3>',
            'body' => '<p>test</p>',
        ));
        $result = $this->plugin->convert(array(), $body);

        $this->assertAttributeEquals($expected, 'items', $this->plugin);
    }

    public function testSetItemsWithBodyHasOnlyImage()
    {
        $this->setUpForTestSetItems();

        $body = '![](sample.jpg)';
        $expected = array(array(
            'image' => '<img src="sample.jpg" alt="">',
            'body'  => '',
        ));
        $result = $this->plugin->convert(array(), $body);

        $this->assertAttributeEquals($expected, 'items', $this->plugin);
    }

    public function testSetItemsWithBodyHasOnlyBody()
    {
        $this->setUpForTestSetItems();

        $body = 'test';
        $expected = array(array(
            'body' => '<p>test</p>',
        ));

        $result = $this->plugin->convert(array(), $body);

        $this->assertAttributeEquals($expected, 'items', $this->plugin);
    }

    public function testSetItemsWithBodyHasOnlyHeading()
    {
        $this->setUpForTestSetItems();

        $body = '### Heading';
        $expected = array(array(
            'heading' => '<h3>Heading</h3>',
            'body'  => '',
        ));

        $result = $this->plugin->convert(array(), $body);

        $this->assertAttributeEquals($expected, 'items', $this->plugin);
    }

    public function testSetItemsWithBodyhasHeadingAndBody()
    {
        $this->setUpForTestSetItems();

        $body = '### Heading' . "\n"
              . 'test';
        $expected = array(array(
            'heading' => '<h3>Heading</h3>',
            'body' => '<p>test</p>',
        ));
        $result = $this->plugin->convert(array(), $body);

        $this->assertAttributeEquals($expected, 'items', $this->plugin);        
    }

    public function testSetItemsWithBodyHasImageAndBody()
    {
        $this->setUpForTestSetItems();

        $body = '![](sample.jpg)' . "\n"
              . 'test';
        $expected = array(array(
            'image' => '<img src="sample.jpg" alt="">',
            'body' => '<p>test</p>',
        ));
        $result = $this->plugin->convert(array(), $body);

        $this->assertAttributeEquals($expected, 'items', $this->plugin);
    }
    
    public function testImageAndHeading()
    {
        $this->setUpForTestSetItems();

        $body = '![](sample.jpg)' . "\n"
              . '### Heading';
        $expected = array(array(
            'image' => '<img src="sample.jpg" alt="">',
            'heading' => '<h3>Heading</h3>',
            'body'  => '',
        ));
        $result = $this->plugin->convert(array(), $body);

        $this->assertAttributeEquals($expected, 'items', $this->plugin);        
    }

    public function testSetItemsWithBodyHasMultiSlides()
    {
        $this->setUpForTestSetItems();

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

    public function testNoIndicatorAndControl()
    {
        $params = array('nobutton');
        $body = '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test' . "\n"
              . '====' . "\n"
              . '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test';
        $result = $this->plugin->convert($params, $body);
        
        $not_expects = array(
            array(
                'tag' => 'ol',
                'attributes' => array(
                    'class' => 'carousel-indicators'
                )
            ),
            array(
                'tag' => 'a',
                'attributes' => array(
                    'class' => 'carousel-control'
                )
            )
        );

        foreach ($not_expects as $not_expected)
        {
            $this->assertNotTag($not_expected, $result);
        }
    }

    public function testNoIndicator()
    {
        $params = array('noindicator');
        $body = '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test' . "\n"
              . '====' . "\n"
              . '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test';
        $result = $this->plugin->convert($params, $body);

        $expected = array(
            'tag' => 'a',
            'attributes' => array(
                'class' => 'carousel-control'
            )
        );
        $not_expected = array(
            'tag' => 'ol',
            'attributes' => array(
                'class' => 'carousel-indicators'
            )
        );

        $this->assertTag($expected, $result);
        $this->assertNotTag($not_expected, $result);
    }

    public function testNoControl()
    {
        $params = array('noslidebutton');
        $body = '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test' . "\n"
              . '====' . "\n"
              . '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test';
        $result = $this->plugin->convert($params, $body);

        $expected = array(
            'tag' => 'ol',
            'attributes' => array(
                'class' => 'carousel-indicators'
            )
        );
        $not_expected = array(
            'tag' => 'a',
            'attributes' => array(
                'class' => 'carousel-control'
            )
        );

        $this->assertTag($expected, $result);
        $this->assertNotTag($not_expected, $result);
    }

    public function testSetIndicatorAndControl()
    {
        $params = array('');
        $body = '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test' . "\n"
              . '====' . "\n"
              . '![](sample.jpg)' . "\n"
              . '### Heading' . "\n"
              . 'test';
        $result = $this->plugin->convert($params, $body);

        $expected = array(
            'tag' => 'ol',
            'attributes' => array(
                'class' => 'carousel-indicators'
            )
        );
        $this->assertTag($expected, $result);

        $expected = array(
            'tag' => 'a',
            'attributes' => array(
                'class' => 'carousel-control'
            )
        );
        $this->assertTag($expected, $result);
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
                        'class' => 'haik-plugin-carousel carousel slide'
                    ),
                ),
            )
        );
        $this->assertTag($expected, $result);
    }
}
