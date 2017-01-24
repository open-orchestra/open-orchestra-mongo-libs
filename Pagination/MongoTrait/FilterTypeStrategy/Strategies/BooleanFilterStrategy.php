<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\FilterType\FilterTypeInterface;

/**
 * Class BooleanFilterStrategy
 */
class BooleanFilterStrategy implements FilterTypeInterface
{
    const FILTER_TYPE =  'boolean';

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
        if ($value === 'true' || $value === '1') {
            return array($name => true);
        } elseif ($value === 'false' || $value === '0') {
            return array($name => false);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'boolean_filter';
    }
}
