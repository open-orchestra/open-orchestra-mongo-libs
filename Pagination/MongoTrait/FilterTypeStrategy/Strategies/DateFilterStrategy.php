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
     * @param string $format
     *
     * @return array
     */
    public function generateFilter($name, $value, $documentName='', $format='')
    {
        if ($format != '') {
            $pattern = '/^'.preg_replace(array('/dd/', '/mm/', '/yyyy/', '/yy/'), '(\d+)', preg_quote($format, '/')).'(.*)$/';

            $positionDay = strpos ($format, 'dd');
            $positionMonth = strpos ($format, 'mm');
            $positionYear = strpos ($format, 'yy');
            $rankDay = 1 + (int) ($positionDay > $positionMonth) + (int) ($positionDay > $positionYear);
            $rankMonth = 1 + (int) ($positionMonth > $positionDay) + (int) ($positionMonth > $positionYear);
            $rankYear = 1 + (int) ($positionYear > $positionDay) + (int) ($positionYear > $positionMonth);

            $replacement = '$'.$rankYear.'-$'.$rankMonth.'-$'.$rankDay.'$4';

            $value = preg_replace($pattern, $replacement, $value);
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
