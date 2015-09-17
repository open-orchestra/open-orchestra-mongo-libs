<?php

namespace OpenOrchestra\Mapping\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\Driver\DriverInterface;
use OpenOrchestra\Mapping\Metadata\MergeableClassMetadataFactoryInterface;
use OpenOrchestra\Mapping\Metadata\PropertySearchMetadataFactoryInterface;

/**
 * Class AnnotationDriver
 */
class AnnotationDriver implements DriverInterface
{
    protected $reader;
    protected $propertySearchMetadataClassFactory;
    protected $mergeableClassMetadataFactory;
    protected $annotationClass;

    /**
     * @param AnnotationReader                       $reader
     * @param PropertySearchMetadataFactoryInterface $propertySearchMetadataFactory
     * @param MergeableClassMetadataFactoryInterface $mergeableClassMetadataFactory
     * @param string                                 $annotationClass
     */
    public function __construct(
        AnnotationReader $reader,
        PropertySearchMetadataFactoryInterface $propertySearchMetadataFactory,
        MergeableClassMetadataFactoryInterface $mergeableClassMetadataFactory,
        $annotationClass
    )
    {
        $this->reader = $reader;
        $this->propertySearchMetadataFactory = $propertySearchMetadataFactory;
        $this->mergeableClassMetadataFactory = $mergeableClassMetadataFactory;
        $this->annotationClass = $annotationClass;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return \Metadata\ClassMetadata|null
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = $this->mergeableClassMetadataFactory->create($class->getName());
        $existAnnotation = false;
        foreach ($class->getProperties() as $reflectionProperty) {
            $propertyMetadata = $this->propertySearchMetadataFactory->create($class->getName(), $reflectionProperty->getName());

            $annotations = $this->reader->getPropertyAnnotations(
                $reflectionProperty,
                $this->annotationClass
            );

            if (!empty($annotations)) {
                $existAnnotation = true;
                foreach ($annotations as $annotation) {
                    if (get_class($annotation) == $this->annotationClass) {
                        $propertyMetadata->key = $annotation->getKey();
                        $propertyMetadata->type = $annotation->getType();
                        if (null === $annotation->getField()) {
                            $propertyMetadata->field = $reflectionProperty->getName();
                        } else {
                            $propertyMetadata->field = $annotation->getField();
                        }
                        $classMetadata->addPropertyMetadata($propertyMetadata);
                    }
                }
            }
        }

        return (true === $existAnnotation) ? $classMetadata : null;
    }
}
