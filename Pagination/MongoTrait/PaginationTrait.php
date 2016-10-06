<?php

namespace OpenOrchestra\Pagination\MongoTrait;

use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Trait PaginationTrait
 */
trait PaginationTrait
{
    use FilterTrait;

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregationQuery();
        $qa = $this->generateFilterForPaginate($qa, $configuration);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param FinderConfiguration|null $configuration
     *
     * @return int
     */
    public function count(FinderConfiguration $configuration = null)
    {
        $qa = $this->createAggregationQuery();
        if (!is_null($configuration)) {
            $qa = $this->generatePreFilter($qa, $configuration);
        }

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param FinderConfiguration $configuration
     *
     * @return mixed
     */
    public function countWithFilter(FinderConfiguration $configuration)
    {
        $qa = $this->createAggregationQuery();
        $qa = $this->generateFilter($qa, $configuration);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param Stage                       $qa
     * @param PaginateFinderConfiguration $configuration
     *
     * @return Stage
     */
    protected function generateFilterForPaginate(Stage $qa, PaginateFinderConfiguration $configuration)
    {
        $qa = $this->generateFilter($qa, $configuration);
        $qa = $this->generateFilterSort($qa, $configuration->getOrder(), $configuration->getDescriptionEntity());
        $qa = $this->generateSkipFilter($qa, $configuration->getSkip());
        $qa = $this->generateLimitFilter($qa, $configuration->getLimit());

        return $qa;
    }

    /**
     * @param Stage        $qa
     * @param integer|null $limit
     *
     * @return Stage
     */
    protected function generateLimitFilter(Stage $qa, $limit = null)
    {
        if (null !== $limit) {
            $qa->limit($limit);
        }

        return $qa;
    }

    /**
     * @param Stage        $qa
     * @param integer|null $skip
     *
     * @return Stage
     */
    protected function generateSkipFilter(Stage $qa, $skip = null)
    {
        if (null !== $skip && $skip > 0) {
            $qa->skip($skip);
        }

        return $qa;
    }
}
