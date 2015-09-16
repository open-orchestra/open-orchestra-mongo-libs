<?php

namespace OpenOrchestra\Mapping\Metadata\Driver;

use Symfony\Component\Yaml\Yaml;

class YamlDriver extends AbstractFileDriverSearch
{
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
            $classMetadata = $this->mergeableClassMetadataFactory->create($class->getName());

            foreach ($data[$class->getName()]["properties"] as $field => $property) {
                $propertyMetadata = $this->propertySearchMetadataFactory->create($class->getName(), $field);
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
