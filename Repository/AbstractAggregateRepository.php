<?php

namespace OpenOrchestra\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Solution\MongoAggregation\Pipeline\Stage;
use Solution\MongoAggregationBundle\AggregateQuery\AggregationQueryBuilder;

/**
 * Class AbstractRepository
 */
abstract class AbstractAggregateRepository extends DocumentRepository
{
    /**
     * @var AggregationQueryBuilder
     */
    private $aggregationQueryBuilder;

    /**
     * @param AggregationQueryBuilder $aggregationQueryBuilder
     */
    public function setAggregationQueryBuilder($aggregationQueryBuilder)
    {
        $this->aggregationQueryBuilder = $aggregationQueryBuilder;
    }

    /**
     * @param string $criteria
     *
     * @return array
     */
    public function findWithTransverseCriteria($criteria)
    {
        return $this->findBy($this->createTransverseCriteria($criteria));
    }

    /**
     * @param string $criteria
     *
     * @return string
     */
    public function createTransverseCriteria($criteria)
    {
        $documentManager = $this->getDocumentManager();
        $documentName = $this->getDocumentName();
        $metaData = $documentManager->getClassMetadata($documentName);
        $collectionName = $metaData->getCollection();
        $dataBase = $documentManager->getDocumentDatabase($documentName);

        $return = $dataBase->execute('db.loadServerScripts();return selectEnumeration({collection: "' . $collectionName . '", criteria: ' . $criteria . '});');

        return (is_array($return) && array_key_exists('ok', $return ) && $return['ok'] == 1) ? $return['retval'] : null;
    }

    /**
     * @param string|null $stage
     *
     * @return Stage
     */
    protected function createAggregationQuery($stage = null)
    {
        return $this->aggregationQueryBuilder->getCollection($this->getClassName())->createAggregateQuery($stage);
    }

    /**
     * @param Stage  $qa
     * @param string $elementName
     * @param string $idSelector
     *
     * @return array
     */
    protected function hydrateAggregateQuery(Stage $qa, $elementName = null, $idSelector = null)
    {
        $contents = $qa->getQuery()->aggregate();
        $contentCollection = array();

        foreach ($contents as $content) {
            if (null !== $elementName) {
                $content = $content[$elementName];
            }

            $content = $this->getDocumentManager()->getUnitOfWork()->getOrCreateDocument($this->getClassName(), $content);
            if ($idSelector) {
                $contentCollection[$content->$idSelector()] = $content;
            } else {
                $contentCollection[] = $content;
            }
        }

        return $contentCollection;
    }

    /**
     * @param Stage $qa
     *
     * @return int
     */
    protected function countDocumentAggregateQuery(Stage $qa)
    {
        $qa->group(array(
            '_id' => null,
            'count' => array('$sum' => 1)
        ));
        $res = $qa->getQuery()->aggregate();

        return (null !== $res[0]['count']) ? $res[0]['count'] : 0;
    }

    /**
     * @param Stage  $qa
     *
     * @return mixed
     */
    protected function singleHydrateAggregateQuery(Stage $qa)
    {
        $aggregateCollection = $this->hydrateAggregateQuery($qa);

        return (null !== $aggregateCollection && isset($aggregateCollection[0])) ? $aggregateCollection[0] : null;
    }
}
