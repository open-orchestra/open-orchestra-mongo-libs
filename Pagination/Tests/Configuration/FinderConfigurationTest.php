<?php

namespace OpenOrchestra\Tests\Pagination\Configuration;

use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DocumentTest
 */
class FinderConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $search
     * @param array|null  $order
     * @param int|null    $limit
     * @param int|null    $skip
     *
     * @dataProvider provideConfigurationCreation
     */
    public function testGenerateFromRequest($descriptionEntity, $search, $order, $limit, $skip)
    {
        $request = $this->createRequest($search, $order, $limit, $skip);
        $configuration = FinderConfiguration::generateFromRequest($request);
        $configuration->setDescriptionEntity($descriptionEntity);
        $this->finderConfigurationTest($configuration, $search, $descriptionEntity);

        $paginateConfiguration = PaginateFinderConfiguration::generateFromRequest($request);
        $paginateConfiguration->setDescriptionEntity($descriptionEntity);
        $this->finderConfigurationTest($paginateConfiguration, $search, $descriptionEntity);
        $this->finderPaginateConfigurationTest($paginateConfiguration, $order, $limit, $skip);
    }

    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $search
     * @param array|null  $order
     * @param int|null    $limit
     * @param int|null    $skip
     *
     * @dataProvider provideConfigurationCreation
     */
    public function testPaginateVarGeneration($descriptionEntity, $search, $order, $limit, $skip)
    {
        $configuration = PaginateFinderConfiguration::generateFromVariable($descriptionEntity, $search);
        $this->finderConfigurationTest($configuration, $search, $descriptionEntity);

        $configuration->setPaginateConfiguration($order, $skip, $limit);
        $this->finderPaginateConfigurationTest($configuration, $order, $limit, $skip);
    }

    /**
     * @return array
     */
    public function provideConfigurationCreation()
    {
        return array(
            array(array(),array(), array(), 0, 1),
            array(null,array('global' =>'fakeSearch'), null, null, null, null),
            array(array(),array('columns' => array()), null, -1, 0),
        );
    }

    /**
     * @param FinderConfiguration $configuration
     * @param array|null          $search
     * @param array|null          $descriptionEntity
     */
    protected function finderConfigurationTest(FinderConfiguration $configuration, $search, $descriptionEntity)
    {
        $this->isTypeOrNull("is_array", $configuration->getSearch(), $search);
        $this->isTypeOrNull("is_array", $configuration->getDescriptionEntity(), $descriptionEntity);
    }


    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array|null                  $order
     * @param int|null                    $limit
     * @param int|null                    $skip
     */
    protected function finderPaginateConfigurationTest(PaginateFinderConfiguration $configuration, $order, $limit, $skip)
    {
        $this->isTypeOrNull("is_array", $configuration->getOrder(), $order);
        $this->isTypeOrNull("is_int", $configuration->getLimit(), $limit);
        $this->isTypeOrNull("is_int", $configuration->getSkip(), $skip);
    }

    /**
     * @param string $method
     * @param mixed  $valueToTest
     * @param mixed  $testValue
     */
    protected function isTypeOrNull($method, $valueToTest, $testValue)
    {
        if($method($testValue)) {
            $this->assertEquals($valueToTest, $testValue);
        } else {
            $this->assertEquals($valueToTest, null);
        }
    }

    /**
     * @param null|string $search
     * @param null|array  $order
     * @param null|int    $limit
     * @param null|int    $skip
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest($search = null, $order = null, $limit = null, $skip = null)
    {
        $request = new Request();
        if($search !== null) {
            $request->request->set('search', $search);
        }
        if($order !== null) {
            $request->request->set('order', $order);
        }
        if($limit !== null) {
            $request->request->set('limit', $limit);
        }
        if($skip !== null) {
            $request->request->set('skip', $skip);
        }

        return $request;
    }
}
