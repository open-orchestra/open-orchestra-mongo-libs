<?php

namespace OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy;

use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeManager;
use Phake;

/**
 * Class Searching for Usages in Project Files...
 */
class FilterTypeManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterTypeManager
     */
    protected $manager;
    protected $filterStrategie;

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->manager = new FilterTypeManager();
        $this->filterStrategie = Phake::mock('OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeInterface');
        Phake::when($this->filterStrategie)->support(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->filterStrategie)->generateFilter(Phake::anyParameters())->thenReturn('fakeFilter');
    }

    /**
     * Test generateFilter
     */
    public function testGenerateFilter()
    {
        $filter = $this->manager->generateFilter('fakeType','fakeName','fakeValue', 'fakeDocumentName');
        $this->assertSame(null, $filter);

        $this->manager->addStrategy($this->filterStrategie);
        $filter = $this->manager->generateFilter('fakeType','fakeName','fakeValue', 'fakeDocumentName');
        $this->assertSame('fakeFilter', $filter);
    }
}
