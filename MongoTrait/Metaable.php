<?php

namespace OpenOrchestra\MongoTrait;

/**
 * Trait Metaable
 */
trait Metaable
{
    /**
     * @var boolean metaIndex
     *
     * @ODM\Field(type="boolean")
     */
    protected $metaIndex = false;

    /**
     * @var boolean metaFollow
     *
     * @ODM\Field(type="boolean")
     */
    protected $metaFollow = false;

    /**
     * @param boolean $metaIndex
     */
    public function setMetaIndex($metaIndex)
    {
        $this->metaIndex = $metaIndex;
    }

    /**
     * @return boolean
     */
    public function getMetaIndex()
    {
        return $this->metaIndex;
    }

    /**
     * @param boolean $metaFollow
     */
    public function setMetaFollow($metaFollow)
    {
        $this->metaFollow = $metaFollow;
    }

    /**
     * @return boolean
     */
    public function getMetaFollow()
    {
        return $this->metaFollow;
    }
}
