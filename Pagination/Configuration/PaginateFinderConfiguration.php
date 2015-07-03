<?php

namespace OpenOrchestra\Pagination\Configuration;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaginateFinderConfiguration
 */
class PaginateFinderConfiguration extends FinderConfiguration
{
    protected $order = null;
    protected $skip = null;
    protected $limit = null;

    /**
     * @param null|array          $order
     * @param null|int            $skip
     * @param null|int            $limit
     */
    public function setPaginateConfiguration($order = null, $skip = null, $limit = null)
    {
        $this->setLimit($limit);
        if(static::isArrayOrNull($order)){
            $this->setOrder($order);
        }
        $this->setSkip($skip);
    }

    /**
     * @param Request $request
     *
     * @return PaginateFinderConfiguration
     */
    public static function generateFromRequest(Request $request)
    {
        $configuration = static::generateFromRequest($request);

        $configuration->setPaginateConfiguration(
            $request->get('order'),
            $request->get('skip'),
            $request->get('limit')
        );

        return $configuration;
    }

    /**
     * @return array order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param null|array $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return int skip
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * @param int $skip
     */
    public function setSkip($skip)
    {
        $this->skip = $this->getIntOrNull($skip);
    }

    /**
     * @return int limit
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $this->getIntOrNull($limit);
    }

    /**
     * @param int|null $value
     *
     * @return int|null
     */
    protected function getIntOrNull($value) {
        return $value === NULL ? NULL : (int) $value;
    }
}
