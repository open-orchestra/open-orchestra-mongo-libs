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
        $finderConfig = new static();
        if(static::isArrayOrNull($columns))
            $finderConfig->setColumns($columns);
        $finderConfig->setSearch($search);
        $finderConfig->setDescriptionEntity($descriptionEntity);

        return $finderConfig;
    }

    /**
     * @param Request $request
     *
     * @return FinderConfiguration
     */
    public static function generateFromRequest(Request $request)
    {
        $finderConfig = new static();
        $columns = $request->get('columns');
        if(static::isArrayOrNull($columns))
            $finderConfig->setColumns($columns);
        $finderConfig->setSearch($request->get('search'));

        return $finderConfig;
    }

    /**
     * @param string $value
     *
     * @return boolean
     */
    protected static function isArrayOrNull($value)
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
        if (static::isArrayOrNull($descriptionEntity)) {
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
