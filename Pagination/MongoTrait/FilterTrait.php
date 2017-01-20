<?php

namespace OpenOrchestra\Pagination\MongoTrait;

use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Pagination\FilterType\FilterTypeManager;
use Solution\MongoAggregation\Pipeline\Stage;

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
     * @param PaginateFinderConfiguration $configuration
     * @param Stage                       $qa
     * @param array                       $searchTypes
     * @param string                      $format
     *
     * @return Stage
     */
    protected function generateFieldsFilter(PaginateFinderConfiguration $configuration, Stage $qa, array $searchTypes, $format='')
    {
        foreach($searchTypes as $name => $type) {
            $qa = $this->generateFilter($configuration, $qa, $type, $name, $name.'.stringValue', $format);
        }

        return $qa;
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param Stage                       $qa
     * @param string                      $type
     * @param string                      $value
     * @param string                      $name
     * @param string                      $format
     *
     * @return Stage
     */
    protected function generateFilter(PaginateFinderConfiguration $configuration, Stage $qa, $type, $value, $name, $format='')
    {

        $value = $configuration->getSearchIndex($value);
        if (null !== $value && $value !== '') {
            $filter = $this->filterTypeManager->generateFilter($type, $name, $value, '', $format);
            if (!empty($filter)) {
                $qa->match($filter);
            }
        }

        return $qa;
    }
}
