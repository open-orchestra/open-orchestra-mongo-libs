<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeInterface;
use OpenOrchestra\Mapping\Reader\SearchMappingReader;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Solution\MongoAggregationBundle\AggregateQuery\AggregationQueryBuilder;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeManager;
use OpenOrchestra\Repository\AbstractAggregateRepository;

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
     * @param FilterTypeManager       $filterTypeManager)
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
        if (2 == count($columnsTree)) {
            list($property, $referenceProperty) = $columnsTree;

            $metadata = $this->documentManager->getMetadataFactory()->getMetadataFor($documentName);
            $targetDocument = $metadata->getFieldMapping($property)['targetDocument'];

            $mapping = $this->searchMappingReader->extractMapping($targetDocument);

            $repository = $this->documentManager->getRepository($targetDocument);

            if ($repository instanceof AbstractAggregateRepository && method_exists($repository, 'findForPaginate')) {
                $repository->setAggregationQueryBuilder($this->aggregationQueryBuilder);
                $repository->setFilterTypeManager($this->filterTypeManager);

                $configuration = PaginateFinderConfiguration::generateFromVariable($mapping, array('columns' => array($referenceProperty => $value)));

                $filter = array();
                $referenceds = $repository->findForPaginate($configuration);
                foreach ($referenceds as $referenced) {
                    $filter[] = array($property.'.$id' => new \MongoId($referenced->getId()));
                }

                if (count($filter) > 0) {
                    return array('$or' => $filter);
                }
            }
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
