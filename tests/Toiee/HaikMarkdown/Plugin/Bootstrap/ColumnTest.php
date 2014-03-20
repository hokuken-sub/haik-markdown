<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Column;

class ColumnTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider parsableTestProvider
     */
    public function testParsable($string, $expected)
    {
        $this->assertEquals($expected, Column::isParsable($string));
    }

    public function parsableTestProvider()
    {
        return array(
            array('12', true),
            array('0', true),
            array('100', true),
            array('-50', false),
            array('10.5', true),
            array('+5', false),
            array('.class-name', false),
            array('2+3', true),
            array('2+3.8', true),
            array('2+0', true),
            array('2+a', false),
            array('3+gb', false),
            array('3-8', false),
            array('4a', false),
            array('4+7b', false),
            array('1.foo.bar', true),
            array('10.fooBarBuzz', true),
            array('11.under_score', true),
            array('15.マルチバイト', false),
            array('2.[foo]', false),
            array('3.(bar)', false),
            array('5+5."quotes"', false),
            array('0.<buzz>', false),
        );
    }

    /**
     * @dataProvider stringProvider
     */
    public function testParseText($string, $expected, $attribute)
    {
        $column = with(new Column())->parseText($string);
        $this->assertAttributeEquals($expected, $attribute, $column);
    }

    public function stringProvider()
    {
        return array(
            array(
                'string'    => '',
                'expected'  => 12,
                'attribute' => 'columnWidth',
            ),
            array(
                'string'    => '-50',
                'expected'  => 12,
                'attribute' => 'columnWidth',
            ),
            array(
                'string'    => '0',
                'expected'  => 12,
                'attribute' => 'columnWidth',
            ),
            array(
                'string'    => '12',
                'expected'  => 12,
                'attribute' => 'columnWidth',
            ),
            array(
                'string'    => '8',
                'expected'  => 8,
                'attribute' => 'columnWidth',
            ),
            array(
                'string'    => '30',
                'expected'  => 12,
                'attribute' => 'columnWidth',
            ),
            array(
                'string'    => '9.class-name',
                'expected'  => 9,
                'attribute' => 'columnWidth',
            ),
            array(
                'string'    => '3.class-name1.class-name2',
                'expected'  => 3,
                'attribute' => 'columnWidth',
            ),
            array(
                'string'    => '3+6',
                'expected'  => 3,
                'attribute' => 'columnWidth',
            ),
            array(
                'string'    => '1+0',
                'expected'  => 0,
                'attribute' => 'offsetWidth',
            ),
            array(
                'string'    => '4+5',
                'expected'  => 5,
                'attribute' => 'offsetWidth',
            ),
            array(
                'string'    => '4+12',
                'expected'  => 11,
                'attribute' => 'offsetWidth',
            ),
            array(
                'string'    => '3+4.class-name',
                'expected'  => 4,
                'attribute' => 'offsetWidth',
            ),
            array(
                'string'    => '1+2.class-name1.class-name2',
                'expected'  => 2,
                'attribute' => 'offsetWidth',
            ),
            array(
                'string'    => '7.class-name',
                'expected'  => 'class-name',
                'attribute' => 'classAttribute',
            ),
            array(
                'string'    => '3+1.class-name',
                'expected'  => 'class-name',
                'attribute' => 'classAttribute',
            ),
            array(
                'string'    => '8.class-name1.class-name2',
                'expected'  => 'class-name1 class-name2',
                'attribute' => 'classAttribute',
            ),
        );
    }

    /**
     * @dataProvider columnDataProvider
     */
    public function testCreateClassAttribute($data, $expected)
    {
        $column = new Column();
        foreach ($data as $key => $value)
        {
            switch ($key)
            {
                case 'columnWidth':
                case 'offsetWidth':
                    $method = 'set' . ucfirst($key);
                    $column->$method($value);
                    break;
                case 'classAttribute':
                    $column->addClassAttribute($value);
            }
        }
        $class_attribute = $column->createClassAttribute();
        $this->assertEquals($expected, $class_attribute);
    }

    public function columnDataProvider()
    {
        return array(
            array(
                'data'     => array(
                    'columnWidth'    => 12,
                    'offsetWidth'    => 0,
                    'classAttribute' => '',
                ),
                'expected' => 'col-sm-12'
            ),
            array(
                'data'     => array(
                    'columnWidth'    => 8,
                    'offsetWidth'    => 0,
                    'classAttribute' => '',
                ),
                'expected' => 'col-sm-8'
            ),
            array(
                'data'     => array(
                    'columnWidth'    => 4,
                    'offsetWidth'    => 8,
                    'classAttribute' => '',
                ),
                'expected' => 'col-sm-4 col-sm-offset-8'
            ),
            array(
                'data'     => array(
                    'columnWidth'    => 1,
                    'offsetWidth'    => 0,
                    'classAttribute' => 'class-name',
                ),
                'expected' => 'col-sm-1 class-name'
            ),
            array(
                'data'     => array(
                    'columnWidth'    => 2,
                    'offsetWidth'    => 8,
                    'classAttribute' => 'class-name',
                ),
                'expected' => 'col-sm-2 col-sm-offset-8 class-name'
            ),
            array(
                'data'     => array(
                    'columnWidth'    => 2,
                    'offsetWidth'    => 0,
                    'classAttribute' => 'class-name1 class-name2',
                ),
                'expected' => 'col-sm-2 class-name1 class-name2'
            ),
        );
    }

    public function testStyleAttribute()
    {
        $column = new Column();
        $column->addStyleAttribute('color:white;');
        $style_attr = $column->getStyleAttribute();
        $expected = 'color:white';
        $this->assertEquals($expected, $style_attr);
        
        $column->addStyleAttribute(';background-color:black;');
        $style_attr = $column->getStyleAttribute();
        $expected = 'color:white;background-color:black';
        $this->assertEquals($expected, $style_attr);

        $column->addStyleAttribute('position:fixed;top:0;left:50px');
        $style_attr = $column->getStyleAttribute();
        $expected = 'color:white;background-color:black;position:fixed;top:0;left:50px';
        $this->assertEquals($expected, $style_attr);
    }

    public function testCreateClassAttributeFromOffsetWidth()
    {
        $column = new Column();
        $column->setOffsetWidth(8);
        $offset_width = $column->getOffsetWidth();
        $expected = 8;
        $this->assertEquals($expected, $offset_width);

        $column = new Column();
        $column->setOffsetWidth(30);
        $offset_width = $column->getOffsetWidth();
        $expected = 11;
        $this->assertEquals($expected, $offset_width);

        $column = new Column();
        $column->setOffsetWidth(0);
        $offset_width = $column->getOffsetWidth();
        $expected = null;
        $this->assertEquals($expected, $offset_width);
    }

    public function testRenderWithOnlyClass()
    {
        $column = new Column();
        $column->addClassAttribute("class-name");
        $html = $column->render();
        $expected = array(
            'tag' => 'div',
            'attributes' => array('class' => 'col-sm-12 class-name'),
        );
        $this->assertTag($expected, $html);
    }

    public function testRenderWithStyle()
    {
        $column = new Column();
        $column->addStyleAttribute('color:red');
        $html = $column->render();
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'col-sm-12',
                'style' => 'color:red',
            ),
        );
        $this->assertTag($expected, $html);
    }

    public function testRenderWithRow()
    {
        $column = new Column();
        $html = $column->renderWithRow();
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'row'
            ),
            'child' => array(
                'tag' => 'div',
                'attributes' => array('class'=>'col-sm-12')
            )
        );
        $this->assertTag($expected, $html);
    }
}
