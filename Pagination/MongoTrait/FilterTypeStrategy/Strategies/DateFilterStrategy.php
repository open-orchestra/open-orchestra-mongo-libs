<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeInterface;
use MongoDate;

/**
 * Class DateFilterStrategy
 */
class DateFilterStrategy implements FilterTypeInterface
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function support($type)
    {
        return $type === 'date';
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
        $value = str_replace("/", "-", trim($value));
        $strTime = strtotime($value);
        if ("00:00:00" ==  date('H:i:s', $strTime)) {
            $dateGte = new MongoDate($strTime);
            $dateLte = new MongoDate(strtotime($value.' + 1 DAY'));
            $filter = array( $name => array( '$gte' => $dateGte, '$lt' => $dateLte));

            return $filter;
        }
        $value = new MongoDate(strtotime($value));
        $filter = array($name => $value);

        return $filter;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'date_filter';
    }
}
