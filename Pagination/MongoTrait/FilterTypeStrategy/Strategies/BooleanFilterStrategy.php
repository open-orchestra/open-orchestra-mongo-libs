<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeInterface;

/**
 * Class BooleanFilterStrategy
 */
class BooleanFilterStrategy implements FilterTypeInterface
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function support($type)
    {
        return $type === 'boolean';
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
        $value = ($value === 'true' || $value === '1') ? true : false;
        $filter = array($name => $value);

        return $filter;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'boolean_filter';
    }
}
