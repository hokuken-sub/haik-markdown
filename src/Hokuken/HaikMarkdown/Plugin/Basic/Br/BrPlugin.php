<?php
namespace Hokuken\HaikMarkdown\Plugin\Basic\Br;

use Hokuken\HaikMarkdown\Plugin\Plugin;

class BrPlugin extends Plugin {

    /**
     * inline call via HaikMarkdown &plugin-name;
     * @params array $params is ignored
     * @params string $body is ignored
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    public function inline($params = array(), $body = '')
    {
        return "<br>\n";
    }

    /**
     * convert call via HaikMarkdown {#plugin-name};
     * @params array $params is ignored
     * @params string $body is ignored
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    public function convert($params = array(), $body = '')
    {
        return '<br>';
    }

}
