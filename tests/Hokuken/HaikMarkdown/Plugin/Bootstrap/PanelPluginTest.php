<?php
use Hokuken\HaikMarkdown\Plugin\Bootstrap\Panel\PanelPlugin;
use Michelf\MarkdownExtra;

class PanelPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('<p>test</p>');
            return $mock;
        });
        $this->plugin = new PanelPlugin($this->parser);
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

    public function testWithoutParamsAndBody()
    {
        $result = $this->plugin->convert();
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'haik-plugin-panel panel panel-default',
            ),
        );
        $this->assertTag($expected, $result);
    }

    /**
     * @dataProvider paramsProvider
     */
    public function testTypeAfterParseParams($params, $expected)
    {
        $this->plugin->convert($params, 'test');
        $this->assertAttributeSame($expected, 'type', $this->plugin);
    }
    
    public function paramsProvider()
    {
        return array(
            array(
                array(),
                'default'
            ),
            array(
                array('primary'),
                'primary'
            ),
            array(
                array('info', 'success'),
                'info'
            ),
            array(
                array('invalid-type'),
                'default'
            ),
            array(
                array('invalid-type', 'danger'),
                'danger'
            ),

            array(
                array('type' => 'primary'),
                'primary'
            ),
            array(
                array('type' => 'info', 'type' => 'success'),
                'success'
            ),
            array(
                array('type' => 'invalid-type'),
                'default'
            ),
            array(
                array('type' => 'invalid-type', 'type' => 'danger'),
                'danger'
            ),
        );
    }

    /**
     * @dataProvider bodyProvider
     */
    public function testSetPartial($body, $contains_head, $contains_body, $contains_footer)
    {
        $params = array();
        $this->plugin->convert($params, $body);

        foreach (array('head', 'body', 'footer') as $partial)
        {
            $attr_name = 'partial' . ucfirst($partial);
            $contains_x = 'contains_' . $partial;
            if ($$contains_x)
            {
                $expected = '<p>test</p>';
            }
            else
            {
                $expected = '';
            }
            $this->assertAttributeSame($expected, $attr_name, $this->plugin);
        }
    }

    public function bodyProvider()
    {
        return array(
            // without delimiter
            array(
                "body",
                false, true, false
            ),
            array(
                "head\n====\nbody",
                true, true, false
            ),
            array(
                "head\n====\nbody\n====\nfooter",
                true, true, true
            ),
        );
    }

    /**
     * @dataProvider bodyProviderForTestPartialHead
     */
    public function testPartialHead($body, $class_attr, $heading_level)
    {
        $this->plugin = new PanelPlugin(new MarkdownExtra());
        $params = array();
        $result = $this->plugin->convert($params, $body);
        $expected = array(
            'ancestor' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'haik-plugin-panel panel panel-default'
                ),
            ),
            'tag' => 'div',
            'attributes' => array(
                'class' => 'panel-head'
            ),
            'child' => array(
                'tag' => 'h' . $heading_level,
                'attributes' => array(
                    'class' => $class_attr
                ),
            )
        );
        $expected = array(
            'tag' => 'h' . $heading_level,
            'attributes' => array(
                'class' => $class_attr,
            ),
        );
        $this->assertTag($expected, $result);
    }

    public function bodyProviderForTestPartialHead()
    {
        return array(
            array(
                "Heading\n=======\n\n====\nbody",
                'panel-title',
                1
            ),
            array(
                "## Heading\n\n====\nbody",
                'panel-title',
                2
            ),
            array(
                "### Heading {.panel-title}\n\n====\nbody",
                'panel-title',
                3
            ),
            array(
                "#### Heading {.class-name}\n\n====\nbody",
                'class-name',
                4
            ),
            array(
                "<h5>Heading</h5>\n\n====\nbody",
                'panel-title',
                5
            ),
            array(
                "<h6 class=\"class-name\">Heading</h6>\n\n====\nbody",
                'class-name',
                6
            ),
            array(
                "<h1 class=\"\">Heading</h1>\n\n====\nbody",
                'panel-title',
                1
            ),
        );
    }

    public function testPartialHeadWithDataAttribute()
    {
        $this->plugin = new PanelPlugin(new MarkdownExtra());
        $params = array();
        $body = "<h1 data-foo=\"bar\">Heading</h1>\n\n====\nbody";
        $result = $this->plugin->convert($params, $body);
        $expected = array(
            'tag' => 'div',
            'child' => array(
                'tag' => 'h1',
                'attributes' => array(
                    'class' => 'panel-title',
                    'data-foo' => 'bar',
                )
            ),
        );

        $this->assertTag($expected, $result);
    }

    public function testPartialHeadWithMultipleHeading()
    {
        $this->plugin = new PanelPlugin(new MarkdownExtra());
        $body = "### Heading\n###Heading\n\n====\nbody";
        $expected = array(
            'tag' => 'div',
            'attributes' => array('class' => 'panel-head'),
            'children' => array(
                'count' => 2,
                'only' => array(
                    'tag' => 'h3',
                    'attributes' => array('class' => 'panel-title')
                )
            )
        );
        $result = $this->plugin->convert(array(), $body);
        $this->assertTag($expected, $result);
    }

    public function testPanelWithColumn()
    {
        $params = array('6');
        $body = "body";
        $result = $this->plugin->convert($params, $body);
        $expected = array(
            'tag' => 'div',
            'attributes' => array('class' => 'row'),
            'child' => array(
                'tag' => 'div',
                'attributes' => array('class' => 'col-sm-6'),
            ),
        );
        $this->assertTag($expected, $result);
    }

    public function testPanelWithColumnHash()
    {
        $params = array('span' => '6');
        $body = "body";
        $result = $this->plugin->convert($params, $body);
        $expected = array(
            'tag' => 'div',
            'attributes' => array('class' => 'row'),
            'child' => array(
                'tag' => 'div',
                'attributes' => array('class' => 'col-sm-6'),
            ),
        );
        $this->assertTag($expected, $result);
    }

}
