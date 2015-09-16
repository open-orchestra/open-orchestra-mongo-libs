<?php

namespace OpenOrchestra\Mapping\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\Driver\DriverInterface;

/**
 * Class AnnotationDriver
 */
class AnnotationDriver implements DriverInterface
{
    protected $reader;
    protected $propertySearchMetadataClass;
    protected $mergeableClassMetadata;
    protected $annotationClass;

    /**
     * @param AnnotationReader $reader
     * @param string           $propertySearchMetadataClass
     * @param string           $mergeableClassMetadata
     * @param string           $annotationClass
     */
    public function __construct(AnnotationReader $reader, $propertySearchMetadataClass, $mergeableClassMetadata, $annotationClass)
    {
        $this->reader = $reader;
        $this->propertySearchMetadataClass = $propertySearchMetadataClass;
        $this->mergeableClassMetadata = $mergeableClassMetadata;
        $this->annotationClass = $annotationClass;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return \Metadata\ClassMetadata|null
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {

        $classMetadata = new $this->mergeableClassMetadata($class->getName());
        $existAnnotation = false;
        foreach ($class->getProperties() as $reflectionProperty) {
            $propertyMetadata = new $this->propertySearchMetadataClass($class->getName(), $reflectionProperty->getName());

            $annotations = $this->reader->getPropertyAnnotations(
                $reflectionProperty,
                $this->annotationClass
            );

            if (!empty($annotations)) {
                $existAnnotation = true;
                foreach($annotations as $annotation) {
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
