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
     * @param string $nodeId
     */
    public function addUseInNode($nodeId)
    {
        if (is_string($nodeId)) {
            $this->useReferences[self::KEY_NODE][$nodeId] = $nodeId;
        }
    }

    /**
     * @param string $nodeId
     */
    public function removeUseInNode($nodeId)
    {
        if (is_string($nodeId)) {
            unset($this->useReferences[self::KEY_NODE][$nodeId]);
        }
    }

    /**
     * @param string $contentId
     */
    public function addUseInContent($contentId)
    {
        if (is_string($contentId)) {
            $this->useReferences[self::KEY_CONTENT][$contentId] = $contentId;
        }
    }

    /**
     * @param string $contentId
     */
    public function removeUseInContent($contentId)
    {
        if (is_string($contentId)) {
            unset($this->useReferences[self::KEY_CONTENT][$contentId]);
        }
    }

    /**
     * @return array
     */
    public function getUseReferences()
    {
        return $this->useReferences;
    }
}
