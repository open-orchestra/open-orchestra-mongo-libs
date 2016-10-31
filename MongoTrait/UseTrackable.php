<?php

namespace OpenOrchestra\MongoTrait;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

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

        if (empty($this->useReferences[$entityType])) {
            unset($this->useReferences[$entityType]);
        }
    }

    /**
     * @param string|null $entityType
     *
     * @return array
     */
    public function getUseReferences($entityType = null)
    {
        if (is_null($entityType)) {

            return $this->useReferences;
        }

        if (isset($this->useReferences[$entityType])) {

            return $this->useReferences[$entityType];
        }

        return array();
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
