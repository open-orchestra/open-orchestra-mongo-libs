<?php

namespace OpenOrchestra\Mapping\Metadata\Driver;

/**
 * Class XmlDriver
 */
class XmlDriver extends AbstractFileDriverSearch
{
    /**
     * Parses the content of the file, and converts it to the desired metadata.
     *
     * @param \ReflectionClass $class
     * @param string $file
     *
     * @return \Metadata\ClassMetadata|null
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $elem = simplexml_load_file($file);
        $classElement = $elem->children();
        $className = $classElement->attributes()->{'name'};
        if (null !== $className && (string)$className == $class->getName()) {
            $classMetadata = $this->mergeableClassMetadataFactory->create($class->getName());

            foreach ($classElement->children() as $fieldElement) {
                $fieldElementAttributes = $fieldElement->attributes();
                $field = (string) $fieldElementAttributes->{'field'};
                $type = (string) $fieldElementAttributes->{'type'};
                $key = (string) $fieldElementAttributes->{'key'};

                $propertyMetadata = $this->propertySearchMetadataFactory->create($class->getName(), $field);
                $propertyMetadata->key = $this->extractKey($key);
                $propertyMetadata->type = ('' !== $type) ? $type : "string";
                $propertyMetadata->field = $field;

                $classMetadata->addPropertyMetadata($propertyMetadata);
            }

            return $classMetadata;
        }

        return null;
    }

    /**
     * @param string $key
     *
     * @return array|string
     */
    protected function extractKey($key)
    {
        $key = array_map('trim', explode(',', $key));
        if (count($key) == 1) {
            return array_shift($key);
        }

        return $key;
    }

    /**
     * Returns the extension of the file.
     *
     * @return string
     */
    protected function getExtension()
    {
        return 'xml';
    }
}
