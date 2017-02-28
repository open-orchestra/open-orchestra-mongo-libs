<?php

namespace OpenOrchestra\MongoTrait;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Trait Keywordable
 */
trait Keywordable
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ODM\ReferenceMany(targetDocument="OpenOrchestra\ModelInterface\Model\KeywordInterface")
     */
    protected $keywords;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param KeywordInterface $keyword
     */
    public function addKeyword(KeywordInterface $keyword)
    {
        $this->keywords->add($keyword);
    }

    /**
     * @param KeywordInterface $keyword
     */
    public function removeKeyword(KeywordInterface $keyword)
    {
        $this->keywords->removeElement($keyword);
    }

    /**
     * Initialize keywords
     */
    public function initializeKeywords()
    {
        $this->keywords = new ArrayCollection();
    }
}
