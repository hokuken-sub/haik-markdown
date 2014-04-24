<?php
namespace Hokuken\HaikMarkdown;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class YamlParams {

    public static function adjustAsFlow($text)
    {
        $_text = $text;
        if ((substr($_text, 0, 1) === '[' && substr($_text, -1, 1) === ']') OR
             (substr($_text, 0, 1) === '{' && substr($_text, -1, 1) === '}'))
        {
            return $text;
        }

        $hash = array();
        $text = preg_replace_callback('#(https?|ftp)://#i', function($matches) use (&$hash)
        {
            $idx = count($hash);
            $hash[$idx] = $matches[0];
            return "\0T{$idx}T\0";
        }, $text);
        
        if (strpos($text, ':') > 0)
        {
            $yaml = '{' . $text . '}';
        }
        else
        {
            $yaml = '[' . $text . ']';
        }
        
        $yaml = preg_replace_callback('/\0T(\d+)T\0/', function($matches) use (&$hash)
        {
            $token = '';
            $idx = $matches[1];
            if (isset($hash[$idx]))
            {
                $token = $hash[$idx];
            }
            return $token;
        }, $yaml);

        return $yaml;
    }

    public static function parse($text)
    {
        try {
            return Yaml::parse($text);
        }
        catch (ParseException $e) {
            return str_getcsv($text, ',', '"', '\\');
        }
    }

}
