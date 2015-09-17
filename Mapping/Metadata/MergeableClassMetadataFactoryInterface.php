<?php

namespace OpenOrchestra\Mapping\Metadata;

use Metadata\MergeableClassMetadata;

/**
 * Class MergeableClassMetadataFactoryInterface
 */
interface MergeableClassMetadataFactoryInterface
{
    /**
     * @param string $name
     *
     * @return MergeableClassMetadata
     */
    public function create($name);
}
