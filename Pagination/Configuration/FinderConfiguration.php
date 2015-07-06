<?php

namespace OpenOrchestra\Pagination\Configuration;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class FinderConfiguration
 */
class FinderConfiguration
{
    protected $descriptionEntity = null;
    protected $columns = null;
    protected $search = null;

    /**
     * Construct
     */
    protected function __construct() {}

    /**
     * @param null|array  $descriptionEntity
     * @param null|array  $columns
     * @param null|string $search
     *
     * @return FinderConfiguration
     */
    public static function generateFromVariable($descriptionEntity = null, $columns = null, $search = null)
    {
        $configuration = new static();
        if ($configuration->isArrayOrNull($columns)) {
            $configuration->setColumns($columns);
        }
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

        $columns = $request->get('columns');
        if ($configuration->isArrayOrNull($columns)) {
            $configuration->setColumns($columns);
        }
        $configuration->setSearch($request->get('search'));

        return $configuration;
    }

    /**
     * @param string $value
     *
     * @return boolean
     */
    protected function isArrayOrNull($value)
    {
        return  is_array($value) || $value === NULL;
    }

    /**
     * @return array descriptionEntity
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
        if ($this->isArrayOrNull($descriptionEntity)) {
            $this->descriptionEntity = $descriptionEntity;
        }
    }

    /**
     * @return array columns
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param null|array $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return string search
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param string $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }
}
