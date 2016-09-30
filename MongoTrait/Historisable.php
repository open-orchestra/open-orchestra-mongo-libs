<?php

namespace OpenOrchestra\MongoTrait;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Model\HistoryInterface;

/**
 * Trait Historisable
 */
trait Historisable
{
    /**
     * @var Collection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\HistoryInterface", strategy="set")
     */
    protected $histories;

    /**
     * Add history
     *
     * @param HistoryInterface $history
     */
    public function addHistory(HistoryInterface $history)
    {
        $this->histories->add($history);
    }

    /**
     * Initialize histories
     */
    protected function initializeHistories()
    {
        $this->histories = new ArrayCollection();
    }

}
