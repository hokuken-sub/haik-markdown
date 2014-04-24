<?php
use Hokuken\HaikMarkdown\YamlParams;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class YamlParamsTest extends PHPUnit_Framework_TestCase {

    public function textProvider()
    {
        return [
            [
                '1, 2, 3',
                [
                    1, 2, 3
                ]
            ],
            [
                'abc, def, ghi',
                [
                    "abc", "def", "ghi"
                ]
            ],
            [
                'foo: bar, hoge: fuga',
                [
                    "foo" => "bar", "hoge" => "fuga"
                ]
            ],
            [
                'http://www.example.com/, foo, bar',
                [
                    "http://www.example.com/", "foo", "bar"
                ]
            ],
            [
                'https://www.example.com/, foo, bar',
                [
                    "https://www.example.com/", "foo", "bar"
                ]
            ],
            [
                'ftp://www.example.com/, foo, bar',
                [
                    "ftp://www.example.com/", "foo", "bar"
                ]
            ],
            [
                '[foo, bar, buzz]',
                [
                    "foo", "bar", "buzz"
                ]
            ],
            [
                '{foo: bar, hoge: fuga}',
                [
                    "foo" => "bar", "hoge" => "fuga"
                ]
            ],
        ];
    }

    /**
     * @dataProvider textProvider
     */
    public function testText($text, $expected)
    {
        $yaml = YamlParams::adjustAsFlow($text);
        $result = Yaml::parse($yaml);
        $this->assertEquals($expected, $result);
    }
}
