<?php

namespace OpenOrchestra\MongoTrait;

use OpenOrchestra\ModelBundle\Document\EmbedStatus;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Trait Statusable
 */
trait Statusable
{
    /**
     * @var StatusInterface $status
     *
     * @ODM\EmbedOne(targetDocument="OpenOrchestra\ModelInterface\Model\EmbedStatusInterface")
     */
    protected $status;

    /**
     * @var bool
     */
    protected $statusChanged = false;

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
}
