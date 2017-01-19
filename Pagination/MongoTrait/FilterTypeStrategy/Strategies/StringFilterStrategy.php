<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\FilterType\FilterTypeInterface;
use MongoRegex;

/**
 * Class StringFilterStrategy
 */
class StringFilterStrategy implements FilterTypeInterface
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function support($type)
    {
        return $type === 'string';
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
        $value = preg_quote($value);
        $filter = array($name => new MongoRegex('/.*'.$value.'.*/i'));

        return $filter;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'string_filter';
    }
}
