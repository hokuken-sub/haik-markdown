<?php
namespace Hokuken\HaikMarkdown\Plugin;

interface SpecialAttributeInterface {

    /**
     * Set special id attribute
     *
     * @param string $id special id attribute
     */
    public function setSpecialIdAttribute($id);

    /**
     * Set special class attribute
     *
     * @param string $class special class attribute
     */
    public function setSpecialClassAttribute($class);

}
