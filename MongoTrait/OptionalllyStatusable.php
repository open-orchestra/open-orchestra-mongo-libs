<?php

namespace OpenOrchestra\MongoTrait;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\MongoTrait\IsStatusable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Trait OptionalllyStatusable
 */
trait OptionalllyStatusable
{
    use Statusable {
        setStatus as traitSetStatus;
    }
    use IsStatusable;

    /**
     * Set status
     *
     * @param StatusInterface|null $status
     */
    public function setStatus(StatusInterface $status = null)
    {
        $this->status = null;
        if ($this->statusable) {
            $this->traitSetStatus($status);
        }
    }

}
