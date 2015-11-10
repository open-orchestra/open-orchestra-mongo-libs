<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeInterface;
use Doctrine\MongoDB\Database;
use OpenOrchestra\Mapping\Reader\SearchMappingReader;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Solution\MongoAggregationBundle\AggregateQuery\AggregationQueryBuilder;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeManager;

/**
 * Class ReferenceFilterStrategy
 */
class ReferenceFilterStrategy implements FilterTypeInterface
{
    protected $documentManager;
    protected $searchMappingReader;
    protected $aggregationQueryBuilder;
    protected $filterTypeManager;

    /**
     * @param DocumentManager         $documentManager
     * @param SearchMappingReader     $searchMappingReader
     * @param AggregationQueryBuilder $aggregationQueryBuilder
     */
    public function __construct(
        DocumentManager $documentManager,
        SearchMappingReader $searchMappingReader,
        AggregationQueryBuilder $aggregationQueryBuilder,
        FilterTypeManager $filterTypeManager)
    {
        $this->documentManager = $documentManager;
        $this->searchMappingReader = $searchMappingReader;
        $this->aggregationQueryBuilder = $aggregationQueryBuilder;
        $this->filterTypeManager = $filterTypeManager;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function support($type)
    {
        return $type === 'reference';
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $documentName
     *
     * @return array
     */
    public function generateFilter($name, $value, $documentName)
    {

        $columnsTree = explode('.', $name);
        if(count($columnsTree) == 2) {
            list($property, $referenceProperty) = $columnsTree;

            $metadata = $this->documentManager->getClassMetadata($documentName);
            $reference = $metadata->getFieldMapping($property);
            $repository = $this->documentManager->getRepository($reference['targetDocument']);
            $repository->setAggregationQueryBuilder($this->aggregationQueryBuilder);
            $repository->setFilterTypeManager($this->filterTypeManager);
            $mapping = $this->searchMappingReader->extractMapping($reference['targetDocument']);
            $configuration = PaginateFinderConfiguration::generateFromVariable($mapping, array('columns' => array($referenceProperty => $value)));
            $referenceds = $repository->findForPaginate($configuration);

            $filter = array();
            foreach ($referenceds as $referenced) {
                $filter[] = array($property.'.$id' => new \MongoId($referenced->getId()));
            }

            if (count($filter) > 0) {
                return array('$or' => $filter);
            }

            return null;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference_filter';
    }
}
