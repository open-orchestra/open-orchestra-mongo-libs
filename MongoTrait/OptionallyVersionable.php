<?php

namespace OpenOrchestra\MongoTrait;

/**
 * Trait OptionallyVersionable
 */
trait OptionallyVersionable
{
    /**
     * @var boolean versionable
     *
     * @ODM\Field(type="boolean")
     */
    protected $versionable = true;

    /**
     * @param boolean $versionable
     */
    public function setVersionable($versionable)
    {
        $this->versionable = $versionable;
    }

    /**
     * @return int
     */
    public function isVersionable()
    {
        return $this->versionable;
    }
}
