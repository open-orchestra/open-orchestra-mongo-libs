<?php

namespace OpenOrchestra\Mapping\Metadata;

use Metadata\MergeableClassMetadata;

/**
 * Class MergeableClassMetadataFactory
 */
class MergeableClassMetadataFactory implements MergeableClassMetadataFactoryInterface
{
    /**
     * @param string $name
     *
     * @return MergeableClassMetadata
     */
    public function create($name)
    {
        return new MergeableClassMetadata($name);
    }
}
