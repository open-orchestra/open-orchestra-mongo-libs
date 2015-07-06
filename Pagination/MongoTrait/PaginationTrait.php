<?php

namespace OpenOrchestra\Pagination\MongoTrait;

use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;

/**
 * Trait PaginationTrait
 */
trait PaginationTrait
{

    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     * @param int|null    $skip
     * @param int|null    $limit
     *
     * @deprecated will be removed in 0.3.0, use findForPaginate instead
     *
     * @return array
     */
    public function findForPaginateAndSearch($descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $configuration = PaginateFinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);
        $configuration->setPaginateConfiguration($order, $skip, $limit);

        return $this->findForPaginate($configuration);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration)
    {
        $qb = $this->createQueryWithOrderedFilter($configuration, $configuration->getOrder());

        $skip = $configuration->getSkip();
        if (null !== $skip && $skip > 0) {
            $qb->skip($skip);
        }

        $limit = $configuration->getLimit();
        if (null !== $limit) {
            $qb->limit($limit);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @return int
     */
    public function count()
    {
        $qb = $this->createQueryBuilder();

        return $qb->count()->getQuery()->execute();
    }

    /**
     * @param array|null $columns
     * @param array|null $descriptionEntity
     * @param array|null $search
     *
     * @deprecated will be removed in 0.3.0, use countWithFilter instead
     *
     * @return int
     */
    public function countWithSearchFilter($descriptionEntity = null, $columns = null, $search = null)
    {
        $config = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);

        return $this->countWithFilter($config);
    }

    /**
     * @param FinderConfiguration $configuration
     *
     * @return mixed
     */
    public function countWithFilter(FinderConfiguration $configuration)
    {
        $qb = $this->createQueryWithFilter($configuration);

        return $qb->count()->getQuery()->execute();
    }
}
