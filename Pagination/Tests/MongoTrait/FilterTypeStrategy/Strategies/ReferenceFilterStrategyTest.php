<?php

namespace OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\Configuration\PaginationRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\ReferenceFilterStrategy;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class ReferenceFilterStrategyTest
 */
class ReferenceTestFilterStrategy extends AbstractTestFilterStrategy
{
    /**
     * @var ReferenceFilterStrategy
     */
    protected $strategy;

    protected $documentManager;
    protected $value = 'fakeValue';
    protected $id0 = '000000000000000000000000';
    protected $id1 = 'aaaaaaaaaaaaaaaaaaaaaaaa';
    protected $documentName = 'fakeDocumentName';

    /**
     * Set up
     */
    public function setUp()
    {
        $targetDocument = 'fakeTargetDocument';
        $mapping = array();

        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $searchMappingReader = Phake::mock('OpenOrchestra\Mapping\Reader\SearchMappingReader');
        $aggregationQueryBuilder = Phake::mock('Solution\MongoAggregationBundle\AggregateQuery\AggregationQueryBuilder');
        $filterTypeManager = Phake::mock('OpenOrchestra\Pagination\FilterType\FilterTypeManager');
        $repository = Phake::mock('OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies\PaginationRepository');

        $getId0 = Phake::mock('OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies\PhakeGetIdInterface');
        $getId1 = Phake::mock('OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies\PhakeGetIdInterface');
        $metadata = Phake::mock('Doctrine\ODM\MongoDB\Mapping\ClassMetadata');

        $referencedDocuments = new ArrayCollection();
        $referencedDocuments->add($getId0);
        $referencedDocuments->add($getId1);

        Phake::when($metadata)->getFieldMapping(Phake::anyParameters())->thenReturn(array('targetDocument' => $targetDocument));
        Phake::when($searchMappingReader)->extractMapping($targetDocument)->thenReturn($mapping);
        Phake::when($this->documentManager)->getClassMetadata($this->documentName)->thenReturn($metadata);
        Phake::when($this->documentManager)->getRepository($targetDocument)->thenReturn($repository);
        Phake::when($getId0)->getId()->thenReturn($this->id0);
        Phake::when($getId1)->getId()->thenReturn($this->id1);
        Phake::when($repository)->findForPaginate(Phake::anyParameters())->thenReturn($referencedDocuments);

        $this->strategy = new ReferenceFilterStrategy($this->documentManager, $searchMappingReader, $aggregationQueryBuilder, $filterTypeManager);

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
        $this->assertEquals($expected, $output);
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
            array('', false),
            array(null, false),
            array('reference', true),
        );
    }

    /**
     * Test with no pagination available
     */
    public function testGenerateFilterWithNoPaginationRepsitory()
    {
        $repository = Phake::mock('OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies\NoPaginationRepository');
        Phake::when($this->documentManager)->getRepository(Phake::anyParameters())->thenReturn($repository);

        $this->assertNull($this->strategy->generateFilter('groups.label', $this->value, $this->documentName));
    }

    /**
     * @param string   $columnsTree
     * @param int|null $countOrFilter
     *
     * @dataProvider provideGenerateFilter
     */
    public function testGenerateFilter($columnsTree, $countOrFilter = null)
    {
        $filter = $this->strategy->generateFilter($columnsTree, $this->value, $this->documentName);
        if (null === $countOrFilter) {
            $this->assertNull($filter);
        } else {
            $this->assertCount($countOrFilter, $filter['$or']);
        }
    }

    /**
     * @return array
     */
    public function provideGenerateFilter()
    {
        return array(
            array('groups.label', 3),
            array('groups'),
            array('groups.$id.label'),
        );
    }

    /**
     * Test get name
     */
    public function testGetName()
    {
        $this->assertEquals('reference_filter', $this->strategy->getName());
    }
}

/**
 * Interface PhakeGetIdInterface
 */
interface PhakeGetIdInterface
{
    /**
     * @return string
     */
    public function getId();
}

/**
 * class PaginationRepository
 */
abstract class PaginationRepository extends AbstractAggregateRepository implements PaginationRepositoryInterface
{
}

/**
 * class NoPaginationRepository
 */
abstract class NoPaginationRepository extends AbstractAggregateRepository
{
}
