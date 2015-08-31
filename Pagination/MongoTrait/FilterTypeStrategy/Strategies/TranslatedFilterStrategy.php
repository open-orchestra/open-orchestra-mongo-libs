<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeInterface;
use Doctrine\MongoDB\Database;

/**
 * Class TranslatedFilterStrategy
 */
class TranslatedFilterStrategy implements FilterTypeInterface
{
    protected $documentManager;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function support($type)
    {
        return $type === 'translatedValue';
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
        $collection = $this->documentManager->getDocumentCollection($documentName);
        $collectionName = $collection->getName();
        $dataBase = $this->documentManager->getDocumentDatabase($documentName);
        $criteria = array($name.'.*.value' =>  new \MongoRegex('/.*'.$value.'.*/i'));
        $filter = $this->parseCriteria($criteria, $collectionName, $dataBase);
        if (!empty($filter)) {

            return $filter;
        }

        return null;
    }


    /**
     * @param array    $criteria
     * @param string   $collectionName
     * @param Database $dataBase
     *
     * @return array
     */
    protected function parseCriteria($criteria, $collectionName, $dataBase)
    {
        foreach ($criteria as $key => $value) {
            if(strpos($key, "*") !== false){
                $res = $this->generateCriteria($key, $value, $collectionName, $dataBase);
                if (!empty($res)) {
                    $criteria['$or'] = $res;
                    unset($criteria[$key]);
                }
            } elseif(is_array($value)) {
                $res = $this->parseCriteria($value, $collectionName, $dataBase);
                $criteria[$key] = $res;
            }
        }

        return $criteria;
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

        $return = $dataBase->command(array(
            "mapreduce" => $collectionName,
            "map" => $map,
            "reduce" => $reduce,
            "out" => array("inline" => 1),
            "scope" => array(
                "preColumn" => "$preColumnn",
                "postColumn" => "$postColumn"
            )
        ));

        $res = array();
        if (is_array($return) && array_key_exists('ok', $return ) && $return['ok'] == 1) {
            foreach($return['results'] as $filter) {
                $res[] = array($filter['_id'] => $value);
            }
        }

        return $res;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translated_filter';
    }
}
