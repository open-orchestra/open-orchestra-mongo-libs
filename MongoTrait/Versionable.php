<?php

namespace OpenOrchestra\MongoTrait;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Trait Versionable
 */
trait Versionable
{
    /**
     * @var int $version
     *
     * @ODM\Field(type="string")
     */
    protected $version;

    /**
     * @var int $version
     *
     * @ODM\Field(type="string")
     */
    protected $versionName;

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $versionName
     */
    public function setVersionName($versionName)
    {
        $this->versionName = $versionName;
    }

    /**
     * @return string
     */
    public function getVersionName()
    {
        return $this->versionName;
    }
}
