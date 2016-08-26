<?php

namespace OpenOrchestra\MongoTrait;

/**
 * Trait UseTrackable
 */
trait UseTrackable
{
    /**
     * @ODM\Field(type="hash")
     */
    protected $useReferences = array();

    /**
     * @param string $entityId
     * @param string $entityType
     */
    public function addUseInEntity($entityId, $entityType)
    {
        if (is_string($entityId) && is_string($entityType)) {
            $this->useReferences[$entityType][$entityId] = $entityId;
        }
    }

    /**
     * @param string $entityId
     * @param string $entityType
     */
    public function removeUseInEntity($entityId, $entityType)
    {
        if (is_string($entityId) && is_string($entityType)) {
            unset($this->useReferences[$entityType][$entityId]);
        }
    }

    /**
     * @return array
     */
    public function getUseReferences()
    {
        return $this->useReferences;
    }

    /**
     * @return boolean
     */
    public function isUsed()
    {
        $countReferences = 0;

        foreach ($this->useReferences as $referencesByType) {
            if (is_array($referencesByType)) {
                $countReferences += count($referencesByType);
            }
        }

        return 0 < $countReferences;
    }
}
