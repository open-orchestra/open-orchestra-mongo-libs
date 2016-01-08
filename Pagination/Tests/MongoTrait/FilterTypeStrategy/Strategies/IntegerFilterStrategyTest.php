<?php

namespace OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\IntegerFilterStrategy;

/**
 * Class IntegerFilterStrategyTest
 */
class IntegerFilterStrategyTest extends AbstractFilterStrategyTest
{
    /**
     * @var IntegerFilterStrategy
     */
    protected $strategy;

    public function setUp()
    {
        $this->strategy = new IntegerFilterStrategy();
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
            array('integer', true),
            array('', false),
            array(null, false),
        );
    }

    /**
     * @param string  $value
     *
     * @dataProvider provideGenerateFilter
     */
    public function testGenerateFilter($value)
    {
        $name = 'fakeName';
        $filter = $this->strategy->generateFilter($name, $value, 'fakeDocumentName');

        $expectedValue = (int) $value;
        $this->assertTrue(isset($filter[$name]));
        $this->assertInternalType('integer',$filter[$name]);
        $this->assertSame($expectedValue,$filter[$name]);
    }

    /**
     * @return array
     */
    public function provideGenerateFilter()
    {
        return array(
            array('5'),
            array('5515'),
            array('55.58'),
        );
    }

    /**
     * Test get name
     */
    public function testGetName()
    {
        $this->assertEquals($this->strategy->getName(), 'integer_filter');
    }
}
