<?php

namespace OpenOrchestra\MongoTrait;

use OpenOrchestra\ModelInterface\Model\KeywordInterface;

/**
 * Trait Keywordable
 */
trait Keywordable
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\EmbedKeywordInterface")
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
}
