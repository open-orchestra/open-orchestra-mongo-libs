<?php

namespace OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\FilterType\FilterTypeInterface;
use MongoDate;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;

/**
 * Class DateFilterStrategy
 */
class DateFilterStrategy implements FilterTypeInterface
{

    protected $contextManager;

    /**
     * @param CurrentSiteIdInterface $contextManager
     */
    public function __construct(
        CurrentSiteIdInterface $contextManager
    ) {
        $this->contextManager = $contextManager;
    }

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
        if ($this->contextManager->getDefaultLocale() == 'en') {
            $value = preg_replace('/(\d+)\/(\d+)\/(\d+)(.*)/', '$3-$1-$2$4', $value);
        } else {
            $value = preg_replace('/(\d+)\/(\d+)\/(\d+)(.*)/', '$3-$2-$1$4', $value);
        }
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
