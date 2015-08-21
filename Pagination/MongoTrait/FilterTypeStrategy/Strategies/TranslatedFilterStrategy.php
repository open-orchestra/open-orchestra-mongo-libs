<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeInterface;

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
        $metaData = $this->documentManager->getClassMetadata($documentName);
        $collectionName = $metaData->getCollection();
        $dataBase = $this->documentManager->getDocumentDatabase($documentName);
        $criteria = '{"'.$name.'.*.value": /.*'.$value.'.*/i}';
        $return = $dataBase->command(array(
            'eval' =>'db.loadServerScripts();return selectEnumeration({collection: "' . $collectionName . '", criteria: ' . $criteria . '});',
            'nolock' => true
        ));

        return (is_array($return) && array_key_exists('ok', $return ) && $return['ok'] == 1) ? $return['retval'] : null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translated_filter';
    }
}
