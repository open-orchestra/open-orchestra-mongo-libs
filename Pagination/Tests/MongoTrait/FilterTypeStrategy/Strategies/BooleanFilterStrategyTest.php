<?php

namespace OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\BooleanFilterStrategy;

/**
 * Class BooleanFilterStrategyTest
 */
class BooleanFilterStrategyTest extends AbstractFilterStrategyTest
{
    /**
     * @var BooleanFilterStrategy
     */
    protected $strategy;

    public function setUp()
    {
        $this->strategy = new BooleanFilterStrategy();
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
            array('boolean', true),
            array('string', false),
            array('integer', false),
            array('', false),
            array(null, false),
        );
    }

    /**
     * @param string  $value
     * @param boolean $filterValue
     *
     * @dataProvider provideGenerateFilter
     */
    public function testGenerateFilter($value, $filterValue)
    {
        $name = 'fakeName';
        $filter = $this->strategy->generateFilter($name, $value, 'fakeDocumentName');
        $this->assertSame(array($name => $filterValue), $filter);
    }

    /**
     * @return array
     */
    public function provideGenerateFilter()
    {
        return array(
            array('true', true),
            array('false', false),
            array('0', false),
            array('1', true),
        );
    }

    /**
     * @param mixed $value
     *
     * @dataProvider provideWrongValue
     */
    public function testGenerateFilterWithWrongSearch($value)
    {
        $name = 'fakeName';
        $this->assertNull($this->strategy->generateFilter($name, $value, 'fakeDocumentName'));
    }

    /**
     * @return array
     */
    public function provideWrongValue()
    {
        return array(
            array('foo', null),
            array('bar', null),
            array(2, null),
        );
    }

    /**
     * Test get name
     */
    public function testGetName()
    {
        $this->assertEquals($this->strategy->getName(), 'boolean_filter');
    }
}
