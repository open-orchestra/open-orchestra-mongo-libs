<?php

namespace OpenOrchestra\Mapping\Reader;

use Metadata\MetadataFactoryInterface;
use OpenOrchestra\Mapping\Metadata\PropertySearchMetadata;

/**
 * Class SearchMappingReader
 */
class SearchMappingReader
{
    protected $metadataFactory;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param mixed $class
     *
     * @return array
     */
    public function extractMapping($class)
    {
        $mapping = array();
        $classMetadata = $this->metadataFactory->getMetadataForClass($class);
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            $mapping[$propertyMetadata->key] = $this->transformPropertyMetadataToArray($propertyMetadata);
        }

        return $mapping;
    }

    /**
     * @param PropertySearchMetadata $propertyMetadata
     *
     * @return array
     */
    protected function transformPropertyMetadataToArray(PropertySearchMetadata $propertyMetadata)
    {
        return array (
            "key" => $propertyMetadata->key,
            "field" => $propertyMetadata->field,
            "type" => $propertyMetadata->type,
        );
    }
}
