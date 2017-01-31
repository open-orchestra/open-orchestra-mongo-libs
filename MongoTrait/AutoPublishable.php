<?php

namespace OpenOrchestra\MongoTrait;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class AutoPublishable
 */
trait AutoPublishable
{
    /**
     * @var \DateTime $publishDate
     *
     * @ODM\Field(type="date")
     */
    protected $publishDate;

    /**
     * @var \DateTime $unpublishDate
     *
     * @ODM\Field(type="date")
     */
    protected $unpublishDate;

    /**
     * @param \DateTime|null $date
     */
    public function setPublishDate($date)
    {
        if (is_null($date) || $date instanceof \DateTime) {
            $this->publishDate = $date;
        }
    }

    /**
     * @return \DateTime
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * @param \DateTime|null $date
     */
    public function setUnpublishDate($date)
    {
        if (is_null($date) || $date instanceof \DateTime) {
            $this->unpublishDate = $date;
        }
    }

    /**
     * @return \DateTime
     */
    public function getUnpublishDate()
    {
        return $this->unpublishDate;
    }
}
