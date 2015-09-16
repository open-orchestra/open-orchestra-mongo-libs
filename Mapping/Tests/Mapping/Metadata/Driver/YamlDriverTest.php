<?php

namespace OpenOrchestra\Mapping\Tests\Mapping\Metadata\Driver;

use Metadata\Driver\FileLocator;
use OpenOrchestra\Mapping\Metadata\Driver\YamlDriver;

/**
 * Class YamlDriverTest
 */
class YamlDriverTest extends AbstractDriverTest
{
    /**
     * Set Up
     */
    public function setUp()
    {
        $dirs = array('OpenOrchestra\Mapping\Tests\Mapping\Metadata\Driver\FakeClass' => __DIR__ . '/yml');
        $fileLocaltor = new FileLocator($dirs);

        $this->driver = new YamlDriver($fileLocaltor,
            $this->propertySearchMetadataClass,
            $this->mergeableClassMetadataClass
        );
    }
}
