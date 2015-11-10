<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeInterface;
use Doctrine\MongoDB\Database;
use OpenOrchestra\Mapping\Reader\SearchMappingReader;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;

/**
 * Class ReferenceFilterStrategy
 */
class ReferenceFilterStrategy implements FilterTypeInterface
{
    protected $documentManager;
    protected $searchMappingReader;

    /**
     * @param DocumentManager     $documentManager
     * @param SearchMappingReader $searchMappingReader
     */
    public function __construct(
        DocumentManager $documentManager,
        SearchMappingReader $searchMappingReader)
    {
        $this->documentManager = $documentManager;
        $this->searchMappingReader = $searchMappingReader;
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
     * @param string   $key
     * @param string   $value
     * @param string   $collectionName
     * @param Database $dataBase
     *
     * @return array
     */
    protected function generateCriteria($key, $value, $collectionName, $dataBase)
    {
        $map = new \MongoCode(
            "function() {
                    for (var key in eval('this.' + preColumn)) {
                        emit(preColumn + '.' + key + '.' + postColumn, null);
                    }
            }"
        );
        $reduce = new \MongoCode("function(k, vals) {  return null; }");

        preg_match('/(.*)\.\*.(.*)/',$key, $column);
        $preColumnn = $column[1];
        $postColumn = $column[2];

        $commandResult = $dataBase->command(array(
            "mapreduce" => $collectionName,
            "map" => $map,
            "reduce" => $reduce,
            "out" => array("inline" => 1),
            "scope" => array(
                "preColumn" => "$preColumnn",
                "postColumn" => "$postColumn"
            )
        ));

        $criteria = array();
        if (is_array($commandResult) && array_key_exists('ok', $commandResult ) && $commandResult['ok'] == 1) {
            foreach ($commandResult['results'] as $filter) {
                $criteria[] = array($filter['_id'] => $value);
            }
        }

        return $criteria;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference_filter';
    }
}
