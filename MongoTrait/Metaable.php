<?php

namespace OpenOrchestra\MongoTrait;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;

/**
 * Trait Metaable
 */
trait Metaable
{
    /**
     * @var Collection $metaKeywords
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\TranslatedValueInterface", strategy="set")
     * @ORCHESTRA\Search(key="metaKeyword", type="translatedValue")
     */
    protected $metaKeywords;

    /**
     * @var Collection $metaDescriptions
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\TranslatedValueInterface", strategy="set")
     * @ORCHESTRA\Search(key="metaDescription", type="translatedValue")
     */
    protected $metaDescriptions;

    /**
     * @var boolean metaIndex
     *
     * @ODM\Field(type="boolean")
     */
    protected $metaIndex = false;

    /**
     * @var boolean metaFollow
     *
     * @ODM\Field(type="boolean")
     */
    protected $metaFollow = false;

    /**
     * @param Collection $metaKeywords
     */
    public function setMetaKeywords(Collection $metaKeywords) {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @param Collection $metaDescription
     */
    public function setMetaDescriptions(Collection $metaDescription) {
        $this->metaDescriptions = $metaDescription;
    }

    /**
     * @param TranslatedValueInterface $metaKeyword
     */
    public function addMetaKeyword(TranslatedValueInterface $metaKeyword)
    {
        $this->metaKeywords->set($metaKeyword->getLanguage(), $metaKeyword);
    }

    /**
     * @param TranslatedValueInterface $metaKeyword
     */
    public function removeMetaKeyword(TranslatedValueInterface $metaKeyword)
    {
        $this->metaKeywords->remove($metaKeyword->getLanguage());
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getMetaKeyword($language = 'en')
    {
        return $this->metaKeywords->get($language)->getValue();
    }

    /**
     * @return Collection
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param TranslatedValueInterface $metaKeyword
     */
    public function addMetaDescription(TranslatedValueInterface $metaDescription)
    {
        $this->metaDescriptions->set($metaDescription->getLanguage(), $metaDescription);
    }

    /**
     * @param TranslatedValueInterface $metaDescription
     */
    public function removeMetaDescription(TranslatedValueInterface $metaDescription)
    {
        $this->metaDescriptions->remove($metaDescription->getLanguage());
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getMetaDescription($language = 'en')
    {
        return $this->metaDescriptions->get($language)->getValue();
    }

    /**
     * @return Collection
     */
    public function getMetaDescriptions()
    {
        return $this->metaDescriptions;
    }

    /**
     * @param boolean $metaIndex
     */
    public function setMetaIndex($metaIndex)
    {
        $this->metaIndex = $metaIndex;
    }

    /**
     * @return boolean
     */
    public function getMetaIndex()
    {
        return $this->metaIndex;
    }

    /**
     * @param boolean $metaFollow
     */
    public function setMetaFollow($metaFollow)
    {
        $this->metaFollow = $metaFollow;
    }

    /**
     * @return boolean
     */
    public function getMetaFollow()
    {
        return $this->metaFollow;
    }

    /**
     * @return array
     */
    public function getTranslatedProperties()
    {
        return array(
            'getMetaKeywords',
            'getMetaDescriptions'
        );
    }
}
