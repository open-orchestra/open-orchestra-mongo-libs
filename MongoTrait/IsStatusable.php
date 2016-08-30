<?php

namespace OpenOrchestra\MongoTrait;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Trait IsStatusable
 */
trait IsStatusable
{
    /**
     * @var boolean $statusable
     *
     * @ODM\Field(type="boolean")
     */
    protected $statusable;

    /**
     * @return boolean
     */
    public function isStatusable()
    {
        return $this->statusable;
    }

    /**
     * @param boolean $statusable
     */
    public function setStatusable($statusable)
    {
        $this->statusable = $statusable;
    }
}