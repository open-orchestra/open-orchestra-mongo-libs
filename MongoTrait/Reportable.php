<?php

namespace OpenOrchestra\MongoTrait;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Model\ReportInterface;

/**
 * Trait Reportable
 */
trait Reportable
{
    /**
     * @var Collection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\ReportInterface", strategy="set")
     */
    protected $reports;

    /**
     * Add report
     *
     * @param ReportInterface $report
     */
    public function addReport(ReportInterface $report)
    {
        $this->reports->add($report);
    }

    /**
     * Initialize reports
     */
    protected function initializeReports()
    {
        $this->reports = new ArrayCollection();
    }

}
