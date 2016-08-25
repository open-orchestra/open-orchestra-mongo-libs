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
     * @param string $contentTypeId
     */
    public function addUseInContentType($contentTypeId)
    {
        if (is_string($contentTypeId)) {
            $this->useReferences[self::KEY_CONTENT_TYPE][$contentTypeId] = $contentTypeId;
        }
    }

    /**
     * @param string $contentTypeId
     */
    public function removeUseInContentType($contentTypeId)
    {
        if (is_string($contentTypeId)) {
            unset($this->useReferences[self::KEY_CONTENT_TYPE][$contentTypeId]);
        }
    }

    /**
     * @param string $mediaId
     */
    public function addUseInMedia($mediaId)
    {
        if (is_string($mediaId)) {
            $this->useReferences[self::KEY_MEDIA][$mediaId] = $mediaId;
        }
    }

    /**
     * @param string $mediaId
     */
    public function removeUseInMedia($mediaId)
    {
        if (is_string($mediaId)) {
            unset($this->useReferences[self::KEY_MEDIA][$mediaId]);
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
