<?php

namespace OpenOrchestra\MongoTrait;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Trait SiteLinkable
 */
trait SiteLinkable
{
    /**
     * @ODM\Field(type="boolean")
     */
    protected $linkedToSite = false;

    /**
     * @return boolean
     */
    public function isLinkedToSite()
    {
        return $this->linkedToSite;
    }

    /**
     * @param boolean $linkedToSite
     */
    public function setLinkedToSite($linkedToSite)
    {
        $this->linkedToSite = $linkedToSite;
    }
}
