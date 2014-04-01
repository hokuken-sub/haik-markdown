<?php
use Toiee\HaikMarkdown\Plugin\Pure\Utility;

class PureUtilityTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider numberProvider
     */
    public function testGetGCD($n, $m, $expected)
    {
        $gcd = Utility::getGCD($n, $m);
        $this->assertEquals($expected, $gcd);
    }

    public function numberProvider()
    {
        return array(
            array(12, 6, 6),
            array(12, 8, 4),
            array(45, 105, 15),
            array(12, 12, 12),
            array(1, 5, 1),
            array(7, 5, 1),
        );
    }
}