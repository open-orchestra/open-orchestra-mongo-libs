<?php

namespace OpenOrchestra\Transformer\Tests;

use OpenOrchestra\Transformer\LuceneToBddTransformer;

/**
 * Class LuceneToBddTransformerTest
 */
class LuceneToBddTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LuceneToBddTransformer
     */
    protected $transformer;

    public function setUp()
    {
        $this->transformer = new LuceneToBddTransformer('keywords');
    }

    /**
     * @param array $value
     * @param array $expected
     *
     * @dataProvider provideReverseTransformValue
     */
    public function testReverseTransform($value, $expected)
    {
        $this->assertEquals($expected, $this->transformer->reverseTransform($value));
    }

    /**
     * @return array
     */
    public function provideReverseTransformValue()
    {
        return array(
            array(array('keywords' => '+(cat:X1 cat:X2) +(author:AAA) +(T1 T2 -T3)'), array('keywords' => '{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"keywords":{"$eq":"author:AAA"}},{"$and":[{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}}]},{"keywords":{"$ne":"T3"}}]}]}')),
            array(array('keywords' => '+(cat:X1 cat:X2)+(author:AAA)+(T1 T2-T3)'), array('keywords' => '{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"keywords":{"$eq":"author:AAA"}},{"$and":[{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}}]},{"keywords":{"$ne":"T3"}}]}]}')),
            array(array('keywords' => '(+(cat:X1 cat:X2)+author:AAA+(+(T1 T2)-T3))'), array('keywords' => '{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"keywords":{"$eq":"author:AAA"}},{"$and":[{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}}]},{"keywords":{"$ne":"T3"}}]}]}')),
            array(array('keywords' => 'test'), array('keywords' => '{"keywords":{"$eq":"test"}}')),
            array(array('keywords' => '(test('), array('keywords' => '{"keywords":{"$eq":"test"}}')),
        );
    }

    /**
     * @param array $value
     * @param array $expected
     *
     * @dataProvider provideTransformValue
     */
    public function testTransform($value, $expected)
    {
        $this->assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return array
     */
    public function provideTransformValue()
    {
        return array(
            array(array('keywords' => '{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"keywords":{"$eq":"author:AAA"}},{"$and":[{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}}]},{"keywords":{"$ne":"T3"}}]}]}'), array('keywords' => '(+(cat:X1 cat:X2)+author:AAA+(+(T1 T2)-T3))')),
            array(array('keywords' => '{"keywords":{"$eq":"test"}}'), array('keywords' => 'test')),
        );
    }
}
