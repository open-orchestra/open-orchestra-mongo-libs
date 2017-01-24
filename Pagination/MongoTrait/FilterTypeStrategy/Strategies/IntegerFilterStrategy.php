<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\FilterType\FilterTypeInterface;

/**
 * Class IntegerFilterStrategy
 */
class IntegerFilterStrategy implements FilterTypeInterface
{
    const FILTER_TYPE =  'integer';

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
        return array($name => (int) $value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'integer_filter';
    }
}
