<?php

namespace OpenOrchestra\Mapping\Tests\Mapping\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use OpenOrchestra\Mapping\Metadata\Driver\AnnotationDriver;

/**
 * Class AnnotationDriverTest
 */
class AnnotationDriverTest extends AbstractDriverTest
{
    /**
     * @var AnnotationDriver
     */
    protected $annotationDriver;

    /**
     * Set Up
     */
    public function setUp()
    {
        $searchAnnotationClass = 'OpenOrchestra\Mapping\Annotations\Search';
        $this->driver = new AnnotationDriver(
            new AnnotationReader(),
            $this->propertySearchMetadataClass,
            $this->mergeableClassMetadataClass,
            $searchAnnotationClass
        );
    }
}
