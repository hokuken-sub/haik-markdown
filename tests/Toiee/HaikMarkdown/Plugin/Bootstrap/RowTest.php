<?php
use Toiee\HaikMarkdown\Plugin\Bootstrap\Row;
use Toiee\HaikMarkdown\Plugin\Bootstrap\Column;

class RowTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider columnsProvider
     */
    public function testConstructor($columns)
    {
        $expected = new Row(array(
            new Column('6'),
            new Column('6')
        ));
        $row = new Row($columns);
        
        foreach ($row as $i => $column)
        {
            $this->assertEquals($expected[$i], $column);
        }
    }

    public function columnsProvider()
    {
        return array(
            // column instances
            array(
                array(
                    new Column('6'),
                    new Column('6')
                )
            ),
            // string column options
            array(
                array(
                    '6',
                    '6',
                )
            ),
            // mixed
            array(
                array(
                    new Column('6'),
                    '6',
                )
            ),
        );
    }

    public function testIterator()
    {
        $columns = array(
            new Column('4'),
            new Column('2+1'),
            new Column('5.class-name'),
        );
        $row = new Row($columns);
        foreach ($row as $i => $column)
        {
            $this->assertEquals($columns[$i], $column);
        }
    }

    public function testArrayAccess()
    {
        $columns = array(
            new Column('4'),
            new Column('2+1'),
            new Column('5.class-name'),
        );
        $row = new Row($columns);
        foreach ($row as $i => $column)
        {
            $this->assertEquals($columns[$i], $row[$i]);
        }
    }

    public function testCount()
    {
        $columns = array(
            new Column('4'),
            new Column('2+1'),
            new Column('5.class-name'),
        );
        $row = new Row($columns);
        $this->assertEquals(count($columns), count($row));
    }

    public function testUnsetColumn()
    {
        $columns = array(
            new Column('1'),
            new Column('2'),
            new Column('3')
        );
        $row = new Row($columns);
        unset($row[1]);
        $expected = new Row(array(
            new Column('1'),
            new Column('3')
        ));
        $this->assertEquals($expected, $row);
    }

    public function testIssetColumn()
    {
        $columns = array(
            new Column('1'),
            new Column('2'),
            new Column('3')
        );
        $row = new Row($columns);
        $this->assertTrue(isset($row[2]));
        $this->assertFalse(isset($row[3]));
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
        $column = new Row();
        $column->addClassAttribute("class-name");
        $html = $column->render();
        $expected = array(
            'tag' => 'div',
            'attributes' => array('class' => 'row class-name'),
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
                'class' => 'row',
                'style' => 'color:red',
            ),
        );
        $this->assertTag($expected, $html);
    }

    public function testRenderWithColumns()
    {
        $columns = array(
            new Column('6'),
            new Column('6')
        );
        $row = new Row($columns);
        $html = $row->render();
        $expected = array(
            'tag' => 'div',
            'attributes' => array(
                'class' => 'row'
            ),
            'children' => array(
                'count' => 2,
                'only' => array(
                    'tag' => 'div',
                    'attributes' => array('class' => 'col-sm-6')
                )
            ),
        );
        $this->assertTag($expected, $html);
    }
}
