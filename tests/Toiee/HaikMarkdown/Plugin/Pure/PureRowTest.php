<?php
use Toiee\HaikMarkdown\Plugin\Pure\Row;
use Toiee\HaikMarkdown\Plugin\Pure\Column;

class PureRowTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->row = new Row();
    }

    public function testSetResponsive()
    {
        // default: responsive
        $result = $this->row->render();
        $responsive_expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'pure-g-r'
            )
        );
        $this->assertTag($responsive_expected, $result);

        // set no-responsive
        $result = $this->row->setResponsive(false)->render();
        $not_responsive_expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'pure-g'
            )
        );
        $this->assertTag($not_responsive_expected, $result);

        // set responsive
        $result = $this->row->setResponsive()->render();
        $this->assertTag($responsive_expected, $result);
    }

    public function testModifyClassAttributeWhenSetResponsive()
    {
        $result = $this->row->addClassAttribute('pure-grid-dummy')->setResponsive(false)->getClassAttribute();
        $expected = 'pure-g pure-grid-dummy';
        $this->assertAttributeEquals($expected, 'classAttribute', $this->row);

        $result = $this->row->setResponsive()->getClassAttribute();
        $expected = 'pure-g-r pure-grid-dummy';
        $this->assertAttributeEquals($expected, 'classAttribute', $this->row);
    }

    public function testRenderWithOnlyClass()
    {
        $row = new Row();
        $row->addClassAttribute("class-name");
        $html = $row->render();
        $expected = array(
            'tag' => 'div',
            'attributes' => array('class' => 'pure-g-r class-name'),
        );
        $this->assertTag($expected, $html);
    }

    public function testRenderWithStyle()
    {
        $column = new Row();
        $column->addStyleAttribute('color:red');
        $html = $column->render();
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'pure-g-r',
                'style' => 'color:red',
            ),
        );
        $this->assertTag($expected, $html);
    }

}
