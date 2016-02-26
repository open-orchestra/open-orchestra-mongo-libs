<?php

namespace OpenOrchestra\MongoTrait;

use OpenOrchestra\ModelBundle\Document\EmbedStatus;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Trait Statusable
 */
trait Statusable
{
    /**
     * @var StatusInterface $status
     *
     * @ODM\EmbedOne(targetDocument="OpenOrchestra\ModelInterface\Model\EmbedStatusInterface")
     * @ORCHESTRA\Search(key="status_label", field="status.labels", type="translatedValue")
     */
    protected $status;

    /**
     * @var bool
     */
    protected $statusChanged = false;

    /**
     * @var bool
     *
     * @ODM\Field(type="boolean")
     */
    protected $currentlyPublished = false;

    /**
     * Set status
     *
     * @param StatusInterface|null $status
     */
    public function setStatus(StatusInterface $status = null)
    {
        $this->status = null;
        $this->statusChanged = true;
        if ($status instanceof StatusInterface) {
            $this->status = EmbedStatus::createFromStatus($status);
        }
    }

    /**
     * Get status
     *
     * @return StatusInterface $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function hasStatusChanged()
    {
        return $this->statusChanged;
    }

    /**
     * @return bool
     */
    public function isCurrentlyPublished()
    {
        return $this->currentlyPublished;
    }

    /**
     * @param bool $currentlyPublished
     */
    public function setCurrentlyPublished($currentlyPublished)
    {
        $this->currentlyPublished = $currentlyPublished;
    }
}
