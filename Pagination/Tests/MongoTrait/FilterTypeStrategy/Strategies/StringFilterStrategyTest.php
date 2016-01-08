<?php

namespace OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\StringFilterStrategy;

/**
 * Class StringFilterStrategyTest
 */
class StringTestFilterStrategy extends AbstractTestFilterStrategy
{
    /**
     * @var StringFilterStrategy
     */
    protected $strategy;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->strategy = new StringFilterStrategy();
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
            array('string', true),
            array('integer', false),
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

        $expectedValue = preg_quote($value);
        $this->assertTrue(isset($filter[$name]));
        $this->assertSame('.*'.$expectedValue.'.*', $filter[$name]->regex);
        $this->assertSame('i', $filter[$name]->flags);
    }

    /**
     * @return array
     */
    public function provideGenerateFilter()
    {
        return array(
            array('fakeValue'),
            array('*fakeValue=!<>|:-'),
        );
    }

    /**
     * Test get name
     */
    public function testGetName()
    {
        $this->assertEquals($this->strategy->getName(), 'string_filter');
    }
}
