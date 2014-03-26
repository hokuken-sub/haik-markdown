<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Icon\IconPlugin;

class IconPluginTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->parser = Mockery::mock('Michelf\MarkdownInterface');
        $this->plugin = new IconPlugin($this->parser);
    }

    public function testInlineMethodExists()
    {
        $this->assertInternalType('string', $this->plugin->inline());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsExceptionWhenCallConvert()
    {
        $this->plugin->convert();
    }

    public function testReturnsEmptyTextWithNoParams()
    {
        $this->assertSame('', $this->plugin->inline());
    }

    /**
     * @dataProvider paramProvider
     */
    public function testParameter($params, $expected)
    {
        $result = $this->plugin->inline($params);
        $this->assertTag($expected, $result);

    }

    public function paramProvider()
    {
        return array(
            'default' => array(
                'icon' => array('search'),
                'expected' => array(
                    'tag' => 'i',
                    'attributes' => array(
                        'class' => 'haik-plugin-icon glyphicon glyphicon-search',
                        'content' => ''
                    ),
                )
            ),
            'specified_multi_icon_takes_first' => array(
                'icon' => array('plus', 'minus'),
                'expected' => array(
                    'tag' => 'i',
                    'attributes' => array(
                        'class' => 'haik-plugin-icon glyphicon glyphicon-plus',
                        'content' => ''
                    ),
                )
            ),
            'contains_head_spaces' => array(
                'icon' => array('  time'),
                'expected' => array(
                    'tag' => 'i',
                    'attributes' => array(
                        'class' => 'haik-plugin-icon glyphicon glyphicon-time',
                        'content' => ''
                    ),
                )
            ),
            'contains_tail_spaces' => array(
                'icon' => array('time  '),
                'expected' => array(
                    'tag' => 'i',
                    'attributes' => array(
                        'class' => 'haik-plugin-icon glyphicon glyphicon-time',
                        'content' => ''
                    ),
                )            ),
        );
    }

    public function testIgnoreBody()
    {
        $result = $this->plugin->inline(array('time'), 'body');
        $expected = array(
            'tag' => 'i',
            'content' => ''
        );
        $this->assertTag($expected, $result);
    }

    /**
     * @dataProvider invalidIconNameProvider
     */
    public function testIgnoreInvalidIconName($invalid_icon_name)
    {
        $valid_icon_name = 'star';
        $params = array($invalid_icon_name, $valid_icon_name);
        $result = $this->plugin->inline($params);
        
        $expected = array(
            'tag' => 'i',
            'attributes' => array(
                'class' => 'haik-plugin-icon glyphicon glyphicon-star'
            ),
        );
        $this->assertTag($expected, $result);
    }

    public function invalidIconNameProvider()
    {
        return array(
            array('time$'),
            array('@@{}$'),
            array('マルチバイト'),
            array('<script>alert("foo")</script>'),
            array('http://www.example.com/'),
            array('# headings'),
            array('* list'),
        );
    }

}
