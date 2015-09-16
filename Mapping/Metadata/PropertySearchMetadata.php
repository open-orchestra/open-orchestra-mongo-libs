<?php

namespace OpenOrchestra\Mapping\Metadata;

use Metadata\PropertyMetadata;

/**
 * Class PropertySearchMetadata
 */
class PropertySearchMetadata extends PropertyMetadata
{
    /**
     * @var array
     */
    public $key;

    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $type;

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->class,
            $this->name,
            $this->key,
            $this->field,
            $this->type,
        ));
    }

    /**
     * @param string $str
     */
    public function unserialize($str)
    {
        list($this->class, $this->name, $this->key, $this->field, $this->type) = unserialize($str);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}