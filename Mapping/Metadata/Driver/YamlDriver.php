<?php

namespace OpenOrchestra\Mapping\Metadata\Driver;

use Metadata\Driver\AbstractFileDriver;
use Metadata\Driver\FileLocatorInterface;
use Symfony\Component\Yaml\Yaml;

class YamlDriver extends AbstractFileDriver
{
    protected $propertySearchMetadataClass;
    protected $mergeableClassMetadata;

    /**
     * @param FileLocatorInterface $locator
     * @param string               $propertySearchMetadataClass
     * @param string               $mergeableClassMetadata
     */
    public function __construct(FileLocatorInterface $locator, $propertySearchMetadataClass, $mergeableClassMetadata)
    {
        parent::__construct($locator);
        $this->propertySearchMetadataClass = $propertySearchMetadataClass;
        $this->mergeableClassMetadata = $mergeableClassMetadata;
    }


    /**
     * @param \ReflectionClass $class
     * @param string           $file
     *
     * @return \Metadata\ClassMetadata
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $data = Yaml::parse($file);
        if (isset($data[$class->getName()]) && isset($data[$class->getName()]["properties"])) {
            $classMetadata = new $this->mergeableClassMetadata($class->getName());

            foreach ($data[$class->getName()]["properties"] as $field => $property) {
                $propertyMetadata = new $this->propertySearchMetadataClass($class->getName(), $field);
                $propertyMetadata->key = $property["key"];
                $propertyMetadata->type = isset($property["type"])? $property["type"]: "string";
                $propertyMetadata->field = $field;

                $classMetadata->addPropertyMetadata($propertyMetadata);
            }

            return $classMetadata;
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getExtension()
    {
        return 'yml';
    }
}
