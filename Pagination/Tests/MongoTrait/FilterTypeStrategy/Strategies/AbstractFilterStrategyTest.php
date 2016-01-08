<?php

namespace OpenOrchestra\Pagination\Tests\MongoTrait\FilterTypeStrategy\Strategies;

/**
 * Class AbstractFilterStrategyTest
 */
class AbstractFilterStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Clean up
     */
    protected function tearDown()
    {
        $refl = new ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }
}