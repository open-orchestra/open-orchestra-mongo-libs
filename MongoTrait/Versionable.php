<?php

namespace OpenOrchestra\MongoTrait;

/**
 * Trait Versionable
 */
trait Versionable
{
    /**
     * @var int $version
     *
     * @ODM\Field(type="int")
     * @ORCHESTRA\Search(key="version", type="integer")
     */
    protected $version = 1;

    /**
     * @param int $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
