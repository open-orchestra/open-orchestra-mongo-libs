<?php

namespace OpenOrchestra\Pagination\MongoTrait;

use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeManager;
use Solution\MongoAggregation\Pipeline\Stage;
use OpenOrchestra\Mapping\Annotations\Search;

/**
 * Trait FilterTrait
 */
trait FilterTrait
{
    /**
     * @var FilterTypeManager
     */
    protected $filterTypeManager;

    /**
     * @param FilterTypeManager $filterTypeManager
     */
    public function setFilterTypeManager(FilterTypeManager $filterTypeManager)
    {
        $this->filterTypeManager = $filterTypeManager;
    }

    /**
     * @param Stage               $qa
     * @param FinderConfiguration $configuration
     *
     * @return Stage
     */
    protected function generateFilter(Stage $qa, FinderConfiguration $configuration)
    {
        if (null !== $configuration->getSearch()) {
            $filterSearch = $this->generateSearchFilter($configuration);
            if (null !== $filterSearch) {
                $qa->match($filterSearch);
            }
        }

        return $qa;
    }

    /**
     * @param FinderConfiguration $configuration
     *
     * @return array|null
     */
    protected function generateSearchFilter(FinderConfiguration $configuration)
    {
        $filter = null;
        $descriptionEntity = $configuration->getDescriptionEntity();

        $filtersColumn = $this->getFilterSearchColumn($configuration->getSearchIndex('columns'), $descriptionEntity);
        $filtersAll = $this->getFilterSearchGlobal($configuration->getSearchIndex('global'), $descriptionEntity);
        if (!empty($filtersAll) || !empty($filtersColumn)) {
            $filter = array('$and' => $filtersColumn);
            if (!empty($filtersAll) && empty($filtersColumn)) {
                $filter = array('$or' => $filtersAll);
            } elseif (!empty($filtersAll) && !empty($filtersColumn)) {
                $filter = array('$and'=>array(
                    array('$and' => $filtersColumn),
                    array('$or' => $filtersAll),
                ));
            }
        }

        return $filter;
    }

    /**
     * @param array|null $searchGlobal
     * @param array|null $descriptionEntity
     *
     * @return array
     */
    protected function getFilterSearchGlobal($searchGlobal, $descriptionEntity)
    {
        $filtersAll = array();

        if (null !== $searchGlobal) {
            foreach ($descriptionEntity as $column) {
                $name = $column['field'];
                $type = $column['type'];
                $searchFilter = $this->generateFilterSearchField($name, $searchGlobal, $type);
                if (null !== $searchFilter) {
                    $filtersAll[] = $searchFilter;
                }
            }
        }

        return $filtersAll;
    }

    /**
     * @param array|null $searchColumns
     * @param array|null $descriptionEntity
     *
     * @return array
     */
    protected function getFilterSearchColumn($searchColumns, $descriptionEntity)
    {
        $filtersColumn = array();

        if (null !== $searchColumns) {
            foreach ($searchColumns as $columnsName => $value) {
                if (isset($descriptionEntity[$columnsName]) && isset($descriptionEntity[$columnsName]['field'])) {
                    $descriptionAttribute = $descriptionEntity[$columnsName];
                    $name = $descriptionAttribute['field'];
                    $type = $descriptionAttribute['type'];

                    if (!empty($name)) {
                        $searchfilter = $this->generateFilterSearchField($name, $value, $type);
                        if (null !== $searchfilter) {
                            $filtersColumn[] = $searchfilter;
                        }
                    }
                }
            }
        }

        return $filtersColumn;
    }

    /**
     * Generate filter for search text in field
     *
     * @param string $name
     * @param string $value
     * @param string $type
     *
     * @return array|null
     */
    protected function generateFilterSearchField($name, $value, $type)
    {
        return $this->filterTypeManager->generateFilter($type, $name, $value, $this->getDocumentName());
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return Array
     */
    protected function generateGroupForFilterSort(PaginateFinderConfiguration $configuration)
    {
        $group = array();

        $sorts = $this->generateArrayForFilterSort(
            $configuration->getOrder(),
            $configuration->getDescriptionEntity(),
            false
        );
        foreach ($sorts as $key => $name) {
            $group = array_merge($group, array(
                $key => array(
                    '$last' => '$'.$name
                )));
        }

        return $group;
    }

    /**
     * @param array|null $order
     * @param array|null $descriptionEntity
     * @param boolean    $returnOrder
     *
     * @return Array
     */
    protected function generateArrayForFilterSort($order = null , $descriptionEntity = null, $returnOrder = true)
    {
        if (null !== $order) {
            $columnsName = $order['name'];
            if (isset($descriptionEntity[$columnsName])){
                $key = '';
                if ($descriptionEntity[$columnsName] instanceof Search) {
                    $key = $descriptionEntity[$columnsName]->getField();
                } elseif (isset($descriptionEntity[$columnsName]['field'])) {
                    $key = $descriptionEntity[$columnsName]['field'];
                }
                $value = $key;
                if ($returnOrder) {
                    $value = ($order['dir'] == 'desc') ? -1 : 1;
                }

                return array(str_replace('.', '_', $key) => $value);
            }
        }

        return array();
    }

    /**
     * @param Stage      $qa
     * @param array|null $order
     * @param array|null $descriptionEntity
     *
     * @return Stage
     */
    protected function generateFilterSort(Stage $qa, $order = null , $descriptionEntity = null)
    {
        $sortArgs = $this->generateArrayForFilterSort($order, $descriptionEntity);
        if (!empty($sortArgs)) {
            $qa->sort($sortArgs);
        }

        return $qa;
    }
}
