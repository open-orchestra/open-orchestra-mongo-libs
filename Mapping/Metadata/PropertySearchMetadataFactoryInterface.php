<?php

namespace OpenOrchestra\Mapping\Metadata;

/**
 * Interface PropertySearchMetadataFactoryInterface
 */
interface PropertySearchMetadataFactoryInterface
{
    /**
     * @param mixed  $class
     * @param string $name
     *
     * @return PropertySearchMetadata
     */
    public function create($class, $name);
}
