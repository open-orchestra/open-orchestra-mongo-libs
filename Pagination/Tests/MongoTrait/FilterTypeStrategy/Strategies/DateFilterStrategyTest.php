<?php

namespace OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\DateFilterStrategy;
use MongoDate;
use Phake;

/**
 * Class DateFilterStrategyTest
 */
class DateTestFilterStrategy extends AbstractTestFilterStrategy
{
    /**
     * @var DateFilterStrategy
     */
    protected $strategy;

    public function setUp()
    {
        $this->strategy = new DateFilterStrategy();
    }

    /**
     * @param string $type
     * @param bool   $expected
     *
     * @dataProvider provideSupport
     */
    public function testSupport($type, $expected)
    {
        $output = $this->strategy->support($type);
        $this->assertEquals($output, $expected);
    }

    /**
     * @return array
     */
    public function provideSupport()
    {
        return array(
            array('boolean', false),
            array('string', false),
            array('integer', false),
            array('date', true),
            array('', false),
            array(null, false),
        );
    }

    /**
     * @param string  $value
     * @param string  $filterValue
     * @param string  $language
     *
     * @dataProvider provideGenerateFilter
     */
    public function testGenerateFilter($value, $filterValue, $format)
    {
        $strategy = new DateFilterStrategy();

        $name = 'fakeName';
        $filterValue = new MongoDate(strtotime($filterValue));
        $filter = $strategy->generateFilter($name, $value, 'fakeDocumentName', $format);
        $this->assertSame($filter[$name]->sec, $filterValue->sec);
        $this->assertSame($filter[$name]->usec, $filterValue->usec);
    }

    /**
     * @return array
     */
    public function provideGenerateFilter()
    {
        return array(
            array("19/10/2015 16:23:12", "19-10-2015 16:23:12", "dd/mm/yy"),
            array("10/19/2015 16:23:12", "2015-10-19 16:23:12", "mm/dd/yy"),
        );
    }

    /**
     * Test generate filter date without hour
     */
    public function testGenerateFilterWithoutHour()
    {
        $name = 'fakeName';
        $date  = "2015-10-21";
        $dateGte =  new MongoDate(strtotime($date));
        $dateLt =  new MongoDate(strtotime($date.' + 1 DAY'));
        $filter = $this->strategy->generateFilter($name, $date, 'fakeDocumentName');
        $this->assertSame($filter[$name]['$gte']->sec, $dateGte->sec);
        $this->assertSame($filter[$name]['$lt']->sec, $dateLt->sec);
    }

    /**
     * Test get name
     */
    public function testGetName()
    {
        $this->assertEquals($this->strategy->getName(), 'date_filter');
    }
}
