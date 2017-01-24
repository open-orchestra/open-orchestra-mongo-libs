<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\Pagination\FilterType\FilterTypeInterface;
use Doctrine\MongoDB\Database;

/**
 * Class MultiLanguagesFilterStrategy
 */
class MultiLanguagesFilterStrategy implements FilterTypeInterface
{
    const FILTER_TYPE =  'multiLanguages';

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
        return $type === self::FILTER_TYPE;
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $documentName
     * @param string $format
     *
     * @return array
     */
    public function generateFilter($name, $value, $documentName='', $format='')
    {
        $collection = $this->documentManager->getDocumentCollection($documentName);
        $collectionName = $collection->getName();
        $dataBase = $this->documentManager->getDocumentDatabase($documentName);
        $key = $name.'.*';
        $value = new \MongoRegex('/.*'.$value.'.*/i');
        $filter = $this->generateCriteria($key, $value, $collectionName, $dataBase);

        if (!empty($filter)) {

            return array('$or' => $filter);
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
                        emit(preColumn + '.' + key, null);
                    }
            }"
        );
        $reduce = new \MongoCode("function(k, vals) {  return null; }");

        preg_match('/(.*)\.\*/',$key, $column);
        $preColumnn = $column[1];

        $commandResult = $dataBase->command(array(
            "mapreduce" => $collectionName,
            "map" => $map,
            "reduce" => $reduce,
            "out" => array("inline" => 1),
            "scope" => array(
                "preColumn" => "$preColumnn"
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
        return 'multi_languages_filter';
    }
}
