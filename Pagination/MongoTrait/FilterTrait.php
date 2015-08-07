<?php

namespace OpenOrchestra\Pagination\MongoTrait;

use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Trait FilterTrait
 */
trait FilterTrait
{
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
                $name = $column->getField();
                $type = $column->getType();
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
                if (isset($descriptionEntity[$columnsName]) && null !== $descriptionEntity[$columnsName]->getField()) {
                    $descriptionAttribute = $descriptionEntity[$columnsName];
                    $name = $descriptionAttribute->getField();
                    $type = $descriptionAttribute->getType();

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
        $filter = null;

        if ($type == 'integer') {
            $filter = array($name => (int) $value);
        } elseif ($type == 'boolean') {
            $value = ($value === 'true' || $value === '1') ? true : false;
            $filter = array($name => $value);
        } elseif ($type == 'string'){
            $value = preg_quote($value);
            $filter = array($name => new \MongoRegex('/.*'.$value.'.*/i'));
        }

        return $filter;
    }

    /**
     * @param Stage       $qa
     * @param array|null  $order
     * @param array|null  $descriptionEntity
     *
     * @return Stage
     */
    protected function generateFilterSort(Stage $qa, $order = null , $descriptionEntity = null)
    {
        if (null !== $order) {
            $columnsName = $order['name'];
            if (isset($descriptionEntity[$columnsName]) && null !== $descriptionEntity[$columnsName]->getField()) {
                $name = $descriptionEntity[$columnsName]->getField();
                $dir = ($order['dir'] == 'desc') ? -1 : 1;
                $qa->sort(array($name => $dir));
            }
        }

        return $qa;
    }
}
