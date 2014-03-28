<?php
use Toiee\HaikMarkdown\Plugin\Pure\Column;

class PureColumnTest extends PHPUnit_Framework_TestCase {

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
            array('2+3', false),
            array('2+3.8', false),
            array('2+0', false),
            array('2+a', false),
            array('3+gb', false),
            array('1-2', true),
            array('2-5', true),
            array('3-8', true),
            array('3-7', true),
            array('6-24', true),
            array('25-24', true),
            array('3-2', true),
            array('-1-2', false),
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
    public function testParseText($string, $expected_numerator, $expected_denominator, $expected_class_attribute)
    {
        $column = with(new Column())->parseText($string);
        $this->assertAttributeEquals($expected_numerator, 'unitNumerator', $column);
        $this->assertAttributeEquals($expected_denominator, 'unitDenominator', $column);
    }

    public function stringProvider()
    {
        return array(
            array(
                'string'    => '',
                'expected_numerator'   => 1,
                'expected_denominator' => 1,
                'expected_class_attribute' => '',
            ),
            array(
                'string'    => '-50',
                'expected_numerator'   => 1,
                'expected_denominator' => 1,
                'expected_class_attribute' => '',
            ),
            array(
                'string'    => '0',
                'expected_numerator'   => 1,
                'expected_denominator' => 1,
                'expected_class_attribute' => '',
            ),
            array(
                'string'    => '24',
                'expected_numerator'   => 1,
                'expected_denominator' => 1,
                'expected_class_attribute' => '',
            ),
            array(
                'string'    => '8',
                'expected_numerator'   => 1,
                'expected_denominator' => 3,
                'expected_class_attribute' => '',
            ),
            array(
                'string'    => '30',
                'expected_numerator'   => 1,
                'expected_denominator' => 1,
                'expected_class_attribute' => '',
            ),
            array(
                'string'    => '9.class-name',
                'expected_numerator'   => 3,
                'expected_denominator' => 8,
                'expected_class_attribute' => 'class-name',
            ),
            array(
                'string'    => '3.class-name1.class-name2',
                'expected_numerator'   => 1,
                'expected_denominator' => 8,
                'expected_class_attribute' => 'class-name1 class-name2',
            ),
            array(
                'string'    => '2-5',
                'expected_numerator'   => 2,
                'expected_denominator' => 5,
                'expected_class_attribute' => '',
            ),
            array(
                'string'    => '5-12',
                'expected_numerator'   => 5,
                'expected_denominator' => 12,
                'expected_class_attribute' => '',
            ),
            array(
                'string'    => '7-24',
                'expected_numerator'   => 7,
                'expected_denominator' => 24,
                'expected_class_attribute' => '',
            ),
            array(
                'string'    => '5-0',
                'expected_numerator'   => 5,
                'expected_denominator' => 24,
                'expected_class_attribute' => '',
            ),
            array(
                'string'    => '5-0.class-name',
                'expected_numerator'   => 5,
                'expected_denominator' => 24,
                'expected_class_attribute' => 'class-name',
            ),
            array(
                'string'    => '2-3.class-name',
                'expected_numerator'   => 2,
                'expected_denominator' => 3,
                'expected_class_attribute' => 'class-name',
            ),
            array(
                'string'    => '19-24.class-name1.class-name2',
                'expected_numerator'   => 19,
                'expected_denominator' => 24,
                'expected_class_attribute' => 'class-name1 class-name2',
            ),
        );
    }

    /**
     * @dataProvider columnDataProvider
     */
    public function testCreateClassAttribute($data, $expected)
    {
        $column = new Column();
        $column->setUnitSize($data['unitNumerator'], $data['unitDenominator']);
        $column->addClassAttribute($data['classAttribute']);
        $class_attribute = $column->createClassAttribute();
        $this->assertEquals($expected, $class_attribute);
    }

    public function columnDataProvider()
    {
        return array(
            array(
                'data'     => array(
                    'unitNumerator'    => 1,
                    'unitDenominator'    => 1,
                    'classAttribute' => '',
                ),
                'expected' => 'pure-u-1-1'
            ),
            array(
                'data'     => array(
                    'unitNumerator'    => 2,
                    'unitDenominator'    => 5,
                    'classAttribute' => '',
                ),
                'expected' => 'pure-u-2-5'
            ),
            array(
                'data'     => array(
                    'unitNumerator'    => 19,
                    'unitDenominator'    => 24,
                    'classAttribute' => '',
                ),
                'expected' => 'pure-u-19-24'
            ),
            array(
                'data'     => array(
                    'unitNumerator'    => 3,
                    'unitDenominator'    => 8,
                    'classAttribute' => 'class-name',
                ),
                'expected' => 'pure-u-3-8 class-name'
            ),
            array(
                'data'     => array(
                    'unitNumerator'    => 4,
                    'unitDenominator'    => 5,
                    'classAttribute' => 'class-name1 class-name2',
                ),
                'expected' => 'pure-u-4-5 class-name1 class-name2'
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

    public function testRenderWithOnlyClass()
    {
        $column = new Column();
        $column->addClassAttribute("class-name");
        $html = $column->render();
        $expected = array(
            'tag' => 'div',
            'attributes' => array('class' => 'pure-u-1-1 class-name'),
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
                'class' => 'pure-u-1-1',
                'style' => 'color:red',
            ),
        );
        $this->assertTag($expected, $html);
    }

}
