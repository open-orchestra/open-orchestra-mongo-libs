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
            if (is_array($propertyMetadata->key)) {
                foreach ($propertyMetadata->key as $key) {
                    $mapping[$key] = $this->transformPropertyMetadataToArray($key, $propertyMetadata);
                }
            } else {
                $mapping[$propertyMetadata->key] = $this->transformPropertyMetadataToArray($propertyMetadata->key, $propertyMetadata);
            }
        }
        return $mapping;
    }

    /**
     * @param string                 $key
     * @param PropertySearchMetadata $propertyMetadata
     *
     * @return array
     */
    protected function transformPropertyMetadataToArray($key, PropertySearchMetadata $propertyMetadata)
    {
        return array (
            "key"   => $key,
            "field" => $propertyMetadata->field,
            "type"  => $propertyMetadata->type,
        );
    }
}
