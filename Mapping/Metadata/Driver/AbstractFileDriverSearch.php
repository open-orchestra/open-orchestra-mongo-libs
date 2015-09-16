<?php

namespace OpenOrchestra\Mapping\Metadata\Driver;

use Metadata\Driver\AbstractFileDriver;
use Metadata\Driver\FileLocatorInterface;
use OpenOrchestra\Mapping\Metadata\MergeableClassMetadataFactoryInterface;
use OpenOrchestra\Mapping\Metadata\PropertySearchMetadataFactoryInterface;

/**
 * Class AbstractFileDriverSearch
 */
abstract class AbstractFileDriverSearch extends AbstractFileDriver
{
    protected $propertySearchMetadataFactory;
    protected $mergeableClassMetadataFactory;

    /**
     * @param FileLocatorInterface                   $locator
     * @param PropertySearchMetadataFactoryInterface $propertySearchMetadataFactory
     * @param MergeableClassMetadataFactoryInterface $mergeableClassMetadataFactory
     */
    public function __construct(
        FileLocatorInterface $locator,
        PropertySearchMetadataFactoryInterface $propertySearchMetadataFactory,
        MergeableClassMetadataFactoryInterface $mergeableClassMetadataFactory
    )
    {
        parent::__construct($locator);
        $this->propertySearchMetadataFactory = $propertySearchMetadataFactory;
        $this->mergeableClassMetadataFactory = $mergeableClassMetadataFactory;
    }
}
