<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy;

/**
 * Interface FilterTypeInterface
 */
interface FilterTypeInterface
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function support($type);

    /**
     * @param string $name
     * @param string $value
     * @param string $documentName
     *
     * @return array
     */
    public function generateFilter($name, $value, $documentName);

    /**
     * @return string
     */
    public function getName();
}
