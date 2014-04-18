<?php
namespace Toiee\HaikMarkdown\Plugin;

use Michelf\MarkdownInterface;

abstract class Plugin implements PluginInterface {

    private static $maxId = 0;

    private $id;

    /** @var MarkdownInterface */
    protected $parser;

    /**
     * Constructor
     */
    public function __construct(MarkdownInterface $parser)
    {
        $this->parser = $parser;
        self::$maxId++;
        $this->id = self::$maxId;
    }

    /**
     * Get plugin ID
     *
     * @return integer ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * convert text to inline element
     * @params array $params
     * @params string $body when {...} was set
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    public function inline($params = array(), $body = '')
    {
        throw new \RuntimeException('not implemented');
    }
    
    /**
     * convert text to block element
     * @params array $params
     * @params string $body when :::\n...\n::: was set
     * @return string converted HTML string
     * @throws RuntimeException when unimplement
     */
    public function convert($params = array(), $body = '')
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * check hash array or not
     *
     * @param array $arr i.e plugin params
     * @return boorean when param is hash return true
     */
    public function isHash($arr)
    {
        return array_values($arr) !== $arr;
    }

}
