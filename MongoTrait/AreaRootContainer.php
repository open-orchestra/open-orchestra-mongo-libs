<?php

namespace OpenOrchestra\MongoTrait;

use OpenOrchestra\ModelInterface\Model\AreaInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class AreaRootContainer
 */
trait AreaRootContainer
{
    /**
     * @var AreaInterface
     *
     * @ODM\EmbedOne(targetDocument="OpenOrchestra\ModelInterface\Model\AreaInterface")
     */
    protected $rootArea;

    /**
     * @param AreaInterface $rootArea
     */
    public function setRootArea(AreaInterface $rootArea)
    {
        $this->rootArea = $rootArea;
    }

    /**
     * @return AreaInterface
     */
    public function getRootArea()
    {
        return $this->rootArea;
    }
}
