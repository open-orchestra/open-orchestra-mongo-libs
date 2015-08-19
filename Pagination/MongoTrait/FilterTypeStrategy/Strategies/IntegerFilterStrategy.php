<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeInterface;

/**
 * Class IntegerFilterStrategy
 */
class IntegerFilterStrategy implements FilterTypeInterface
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function support($type)
    {
        return $type === 'integer';
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
