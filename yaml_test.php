<?php
require './vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

$yaml = '
- path
- popup
- aroundr
- class: hoge
- style: "color:red;ho,ge"
';

$yaml = '[path, popup, aroundr, class: hoge, "style:hoge": "color: red; background-image: hogejpg", foo: bar]';

var_dump(Yaml::parse($yaml));
/*

$yaml = '
::: section

----
- class: hoge
- foo: bar
:::
';


[path, popup, aroundr, class:hoge, style:"~"]

{
    class: hoge,
    style: "~",
    path: ""
}
*/