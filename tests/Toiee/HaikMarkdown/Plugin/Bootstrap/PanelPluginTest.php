<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Panel\PanelPlugin;

class PanelPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface', function($mock)
        {
            $mock->shouldReceive('transform')->andReturn('<div>test</div>');
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
            'child' => array(
                'tag' => 'div',
                'attributes' => array(
                    'class' => 'panel-body',
                    'content' => '',
                ),
            )
        );
        $this->assertTag($expected, $result);
    }

    /**
     * @dataProvider paramsProvider
     */
    public function testTypeAfterParseParams($params, $expected)
    {
        $this->plugin->convert($params, 'test');
        $this->assertAttributeSame($expected, $this->plugin);
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
                false, true, true
            ),
        );
    }

    /**
     * @dataProvider bodyProviderForTestPartialHead
     */
    public function testPartialHead($body, $class_attr, $heading_level)
    {
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
                "### Heading {.panel-head}\n\n====\nbody",
                'panel-title',
                3
            ),
            array(
                "#### Heading {.class-name}\n\n====\nbody",
                'class-name',
                4
            ),
            array(
                '<h5>Heading</h5>\n\n====\nbody',
                'panel-title',
                5
            ),
            array(
                '<h6 class="class-name">Heading</h6>\n\n====\nbody',
                'class-name',
                5
            ),
            //ignore greater than heading level 7
            array(
                '<h7>Heading</h7>\n\n====\nbody',
                '',
                7
            ),
            //through other attributes of heading tag
            array(
                '<h1 data-foo="bar">Heading</h1>',
                'panel-title',
                1
            ),
        );
    }

    public function testPartialHeadWithMultipleHeading()
    {
        $body = "### Heading\n###Heading\n\n====\nbody";
        $expected = array(
            'tag' => 'div',
            'attributes' => array('class' => 'panel-head'),
            'children' => array(
                'count' => 2,
                'only' => array(
                    'tag' => 'h3',
                    'attributes' => 'panel-title'
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

}
