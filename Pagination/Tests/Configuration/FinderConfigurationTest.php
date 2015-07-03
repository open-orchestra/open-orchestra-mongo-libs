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
     * @param string|null $search
     * @param array|null  $columns
     * @param array|null  $descriptionEntity
     * @param array|null  $order
     * @param int|null    $limit
     * @param int|null    $skip
     *
     * @dataProvider provideConfigurationCreation
     */
    public function testGenerateFromRequest($descriptionEntity, $search, $columns, $order, $limit, $skip)
    {
        $request = $this->createRequest($search, $columns, $order, $limit, $skip);
        $configuration = FinderConfiguration::generateFromRequest($request);
        $configuration->setDescriptionEntity($descriptionEntity);
        $this->finderConfigurationTest($configuration, $search, $descriptionEntity, $columns);

        $paginateConfiguration = PaginateFinderConfiguration::generateFromRequest($request);
        $paginateConfiguration->setDescriptionEntity($descriptionEntity);
        $this->finderConfigurationTest($paginateConfiguration, $search, $descriptionEntity, $columns);
        $this->finderPaginateConfigurationTest($paginateConfiguration, $order, $limit, $skip);
    }

    /**
     * @param null|string $search
     * @param array|null  $columns
     * @param array|null  $descriptionEntity
     * @param array|null  $order
     * @param int|null    $limit
     * @param int|null    $skip
     *
     * @dataProvider provideConfigurationCreation
     */
    public function testPaginateVarGeneration($descriptionEntity, $search, $columns, $order, $limit, $skip)
    {
        $configuration = PaginateFinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);
        $this->finderConfigurationTest($configuration, $search, $descriptionEntity, $columns);

        $configuration->setPaginateConfiguration($order, $skip, $limit);
        $this->finderPaginateConfigurationTest($configuration, $order, $limit, $skip);
    }

    /**
     * @return array
     */
    public function provideConfigurationCreation()
    {
        return array(
            array('','search', array(), array(), 0, 1),
            array(null,'', null, null, null, null),
            array('','search', 'string', 'string', 'string', array()),
        );
    }

    /**
     * @param FinderConfiguration $configuration
     * @param string              $search
     * @param null|string|array   $descriptionEntity
     * @param null|string|array   $columns
     */
    protected function finderConfigurationTest(FinderConfiguration $configuration, $search, $descriptionEntity, $columns)
    {
        $this->isTypeOrNull("is_string", $configuration->getSearch(), $search);
        $this->isTypeOrNull("is_array", $configuration->getDescriptionEntity(), $descriptionEntity);
        $this->isTypeOrNull("is_array", $configuration->getColumns(), $columns);
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
     * @param mixed $valueToTest
     * @param mixed $testValue
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
     * @param null|array  $columns
     * @param null|array  $order
     * @param null|int    $limit
     * @param null|int    $skip
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest($search = null, $columns = null, $order = null, $limit = null, $skip = null)
    {
        $request = new Request();
        if($search !== NULL)
            $request->request->set('search', $search);
        if($columns !== NULL)
            $request->request->set('columns', $columns);
        if($order !== NULL)
            $request->request->set('order', $order);
        if($limit !== NULL)
            $request->request->set('limit', $limit);
        if($skip !== NULL)
            $request->request->set('skip', $skip);

        return $request;
    }
}
