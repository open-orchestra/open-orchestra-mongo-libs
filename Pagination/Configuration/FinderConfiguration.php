<?php

namespace OpenOrchestra\Pagination\Configuration;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class FinderConfiguration
 */
class FinderConfiguration
{
    protected $descriptionEntity = null;
    protected $search = null;

    /**
     * Construct
     */
    protected function __construct() {}

    /**
     * @param null|array  $descriptionEntity
     * @param null|array  $search
     *
     * @return FinderConfiguration
     */
    public static function generateFromVariable($descriptionEntity = null, $search = null)
    {
        $configuration = new static();
        $configuration->setSearch($search);
        $configuration->setDescriptionEntity($descriptionEntity);

        return $configuration;
    }

    /**
     * @param Request $request
     *
     * @return FinderConfiguration
     */
    public static function generateFromRequest(Request $request)
    {
        $configuration = new static();
        $configuration->setSearch($request->get('search'));

        return $configuration;
    }

    /**
     * @return null|array descriptionEntity
     */
    public function getDescriptionEntity()
    {
        return $this->descriptionEntity;
    }

    /**
     * @param null|array $descriptionEntity
     */
    public function setDescriptionEntity($descriptionEntity)
    {
        $this->descriptionEntity = $descriptionEntity;
    }

    /**
     * @return null|array
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param string $index
     *
     * @return mixed search
     */
    public function getSearchIndex($index)
    {
        return isset($this->search[$index]) ? $this->search[$index] : null;
    }

    /**
     * @param array $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }
}
