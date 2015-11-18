<?php

namespace OpenOrchestra\MongoTrait;

/**
 * Class SoftDeleteable
 */
trait SoftDeleteable
{
    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     * @ORCHESTRA\Search(key="deleted", type="boolean")
     */
    protected $deleted = false;

    /**
     * Set deleted
     *
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * Get deleted
     *
     * @return boolean $deleted
     */
    public function isDeleted()
    {
        return $this->deleted;
    }
}
