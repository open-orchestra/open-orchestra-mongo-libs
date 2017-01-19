<?php

namespace OpenOrchestra\Repository;

/**
 * Trait ReferenceAggregateFilterTrait
 */
trait ReferenceAggregateFilterTrait
{
    /**
     * @param string $property
     * @param array  $filter
     *
     * @return array
     */
    protected function getReferenceFilter($property, array $filter)
    {
        $class = $this->getClassMetadata()->getFieldMapping($property)['targetDocument'];
        $qa = $this->aggregationQueryBuilder->getCollection($class)->createAggregateQuery();
        $qa->match($filter);

        $references = $qa->getQuery()->aggregate()->toArray();
        $filterRef = array(array($property.'.$id' => new \MongoId()));
        foreach ($references as $reference) {
            $filterRef[] = array($property.'.$id' =>$reference['_id']);
        }

        return array('$or' => $filterRef);
    }
}
