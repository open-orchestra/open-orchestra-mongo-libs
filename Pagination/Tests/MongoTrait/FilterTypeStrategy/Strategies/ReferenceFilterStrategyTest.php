<?php

namespace OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies;

use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\ReferenceFilterStrategy;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ReferenceFilterStrategyTest
 */
class ReferenceFilterStrategyTest extends \PHPUnit_Framework_TestCase
{
    protected $value = 'fakeValue';
    protected $documentName = 'fakeDocumentName';
    protected $id0 = '000000000000000000000000';
    protected $id1 = 'aaaaaaaaaaaaaaaaaaaaaaaa';
    /**
     * @var ReferenceFilterStrategy
     */
    protected $strategy;

    /**
     * Set up
     */
    public function setUp()
    {
        $targetDocument = 'fakeTargetDocument';
        $mapping = array();

        $documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $searchMappingReader = Phake::mock('OpenOrchestra\Mapping\Reader\SearchMappingReader');
        $aggregationQueryBuilder = Phake::mock('Solution\MongoAggregationBundle\AggregateQuery\AggregationQueryBuilder');
        $filterTypeManager = Phake::mock('OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\FilterTypeManager');
        $repository = Phake::mock('OpenOrchestra\Repository\AbstractAggregateRepository');
        $metadata = Phake::mock('Doctrine\ODM\MongoDB\Mapping\ClassMetadata');
        $getId0 = Phake::mock('OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies\PhakeGetIdInterface');
        $getId1 = Phake::mock('OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies\PhakeGetIdInterface');
        $metadata = Phake::mock('Doctrine\ODM\MongoDB\Mapping\ClassMetadata');

        $referencedDocuments = new ArrayCollection();
        $referencedDocuments->add($getId0);
        $referencedDocuments->add($getId1);

        Phake::when($metadata)->getFieldMapping(Phake::anyParameters())->thenReturn(array('targetDocument' => $targetDocument));
        Phake::when($searchMappingReader)->extractMapping($targetDocument)->thenReturn($mapping);
        Phake::when($documentManager)->getClassMetadata($this->documentName)->thenReturn($metadata);
        Phake::when($documentManager)->getRepository($targetDocument)->thenReturn($repository);
        Phake::when($getId0)->getId()->thenReturn($this->id0);
        Phake::when($getId1)->getId()->thenReturn($this->id1);
        Phake::when($repository)->findForPaginate(Phake::anyParameters())->thenReturn($referencedDocuments);

        $this->strategy = new ReferenceFilterStrategy($documentManager, $searchMappingReader, $aggregationQueryBuilder, $filterTypeManager);

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
     * @param string $columnsTree
     * @param array  $expectedFilter
     *
     * @dataProvider provideGenerateFilter
     */
    public function testGenerateFilter($columnsTree, $expectedFilter)
    {
        $filter = $this->strategy->generateFilter($columnsTree, $this->value, $this->documentName);
        $this->assertEquals($expectedFilter, $filter);
    }

    /**
     * @return array
     */
    public function provideGenerateFilter()
    {
        return array(
            array('groups.label', array(
                '$or' => array(
                    array('groups.$id' => new \MongoId($this->id0)),
                    array('groups.$id' => new \MongoId($this->id1)),
                )
            )),
            array('groups', null),
            array('groups.$id.label', null),
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