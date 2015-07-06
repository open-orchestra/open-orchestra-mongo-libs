<?php

namespace OpenOrchestra\Pagination\MongoTrait;

use OpenOrchestra\Pagination\Configuration\FinderConfiguration;

/**
 * Trait FilterTrait
 */
trait FilterTrait
{
    /**
     * @param string $value
     * @param string $type
     *
     * @return mixed
     */
    protected function getFilterSearchField($value, $type)
    {
        if ($type == 'integer') {
            $filter = (int) $value;
        } elseif ($type == 'boolean') {
            $value = ($value === 'true' || $value === '1') ? true : false;
            $filter = $value;
        } else {
            $value = preg_quote($value);
            $filter = new \MongoRegex('/.*' . $value . '.*/i');
        }

        return $filter;
    }

    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     *
     * @deprecated will be removed in 0.3.0, use createQueryWithFilter instead
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function createQueryWithSearchFilter($descriptionEntity = null, $columns = null, $search = null)
    {
        $config = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);

        return $this->createQueryWithFilter($config);
    }

    /**
     * @param FinderConfiguration $configuration
     *
     * @return mixed
     */
    protected function createQueryWithFilter(FinderConfiguration $configuration)
    {
        $qb = $this->createQueryBuilder();

        $columns = $configuration->getColumns();
        $descriptionEntity = $configuration->getDescriptionEntity();
        $search = $configuration->getSearch();

        if (null !== $columns) {
            foreach ($columns as $column) {
                $columnsName = $column['name'];
                if (isset($descriptionEntity[$columnsName]) && isset($descriptionEntity[$columnsName]['key'])) {
                    $descriptionAttribute = $descriptionEntity[$columnsName];
                    $name = $descriptionAttribute['key'];
                    $type = isset($descriptionAttribute['type']) ? $descriptionAttribute['type'] : null;
                    if ($column['searchable'] && !empty($column['search']['value']) && !empty($name)) {
                        $value = $column['search']['value'];
                        $qb->addAnd($qb->expr()->field($name)->equals($this->getFilterSearchField($value, $type)));
                    }
                    if (!empty($search) && $column['searchable'] && !empty($name)) {
                        $qb->addOr($qb->expr()->field($name)->equals($this->getFilterSearchField($search, $type)));
                    }
                }
            }
        }

        return $qb;
    }

    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     *
     * @deprecated will be removed in 0.3.0, use createQueryWithOrderedFilter instead
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function createQueryWithSearchAndOrderFilter($descriptionEntity = null, $columns = null, $search = null, $order = null)
    {
        $config = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);

        return $this->createQueryWithOrderedFilter($config, $order);
    }

    /**
     * @param FinderConfiguration $configuration
     * @param array|null          $order
     *
     * @return mixed
     */
    protected function createQueryWithOrderedFilter(FinderConfiguration $configuration, $order)
    {
        $qb = $this->createQueryWithFilter($configuration);

        $columns = $configuration->getColumns();
        if (null !== $order && null !== $columns) {
            foreach ($order as $orderColumn) {
                $numberColumns = $orderColumn['column'];
                if ($columns[$numberColumns]['orderable']) {
                    if (!empty($columns[$numberColumns]['name'])) {
                        $columnsName = $columns[$numberColumns]['name'];
                        $descriptionEntity = $configuration->getDescriptionEntity();
                        if (isset($descriptionEntity[$columnsName]) && isset($descriptionEntity[$columnsName]['key'])) {
                            $name = $descriptionEntity[$columnsName]['key'];
                            $dir = ($orderColumn['dir'] == 'desc') ? -1 : 1;
                            $qb->sort($name, $dir);
                        }
                    }
                }
            }
        }

        return $qb;
    }
}
