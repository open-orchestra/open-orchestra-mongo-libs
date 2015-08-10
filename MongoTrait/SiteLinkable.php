<?php

namespace OpenOrchestra\MongoTrait;

/**
 * Trait SiteLinkable
 */
trait SiteLinkable
{
    /**
     * @ODM\Field(type="boolean")
     * @ORCHESTRA\Search(key="linked_to_site", type="boolean")
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
