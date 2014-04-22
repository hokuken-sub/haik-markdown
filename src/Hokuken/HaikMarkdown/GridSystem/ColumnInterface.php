<?php
namespace Hokuken\HaikMarkdown\GridSystem;

interface ColumnInterface {

    /**
     * Parse text to column data
     *
     * @param  string $text column string
     * @return $this for method chain
     */
    public function parseText($text = '');

    /**
     * Add Class Attribute
     *
     * @param string additional class attribute
     * @return $this for method chain
     */
    public function addClassAttribute($class_attr = '');

    /**
     * Get class attribute without classes relate grid-layout
     *
     * @return string class attribute
     */
    public function getClassAttribute();

    /**
     * Add Style Attribute
     *
     * @param string additional style attribute
     * @return $this for method chain
     */
    public function addStyleAttribute($style_declarations = '');

    /**
     * Get style attribute
     *
     * @return string style attribute
     */
    public function getStyleAttribute();

    /**
     * Set content
     *
     * @param mixed $content
     * @return $this for method chain
     */
    public function setContent($content);

    /**
     * Get content
     *
     * @return string content
     */
    public function getContent();

    /**
     * Create and Get full class attribute
     *
     * @return string full class attribute
     */
    public function createClassAttribute();

    /**
     * Make html of column unit
     *
     * @return string html of column unit
     */
    public function render();

    /**
     * Determine provided text is parsable
     *
     * @param string $text text may be parsable
     * @return boolean the text is parsable?
     */
    public static function isParsable($text);

}
