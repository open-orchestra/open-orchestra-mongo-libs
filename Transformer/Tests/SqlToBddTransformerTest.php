<?php

namespace OpenOrchestra\Transformer\Tests;

use OpenOrchestra\Transformer\SqlToBddTransformer;

/**
 * Class SqlToBddTransformerTest
 */
class SqlToBddTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SqlToBddTransformer
     */
    protected $transformer;

    public function setUp()
    {
        $this->transformer = new SqlToBddTransformer('keywords');
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
            array(array('keywords' => '((cat:X1 OR cat:X2) AND author:AAA) OR (T1 OR T2 OR NOT T3)'), array('keywords' => '{"$or":[{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"keywords":{"$eq":"author:AAA"}}]},{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}},{"keywords":{"$ne":"T3"}}]}]}')),
            array(array('keywords' => '(cat:X1 OR cat:X2) AND (author:AAA) AND (T1 OR T2 OR NOT T3)'), array('keywords' => '{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"$and":[{"keywords":{"$eq":"author:AAA"}}]},{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}},{"keywords":{"$ne":"T3"}}]}]}')),
            array(array('keywords' => 'test'), array('keywords' => '{"$and":[{"keywords":{"$eq":"test"}}]}')),
            array(array('keywords' => '(test('), array('keywords' => '{"$and":[{"keywords":{"$eq":"(test("}}]}')),
            array(array('keywords' => '(test)'), array('keywords' => '{"$and":[{"$and":[{"keywords":{"$eq":"test"}}]}]}')),
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
            array(array('keywords' => '{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"keywords":{"$eq":"author:AAA"}},{"$and":[{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}}]},{"keywords":{"$ne":"T3"}}]}]}'), array('keywords' => '((cat:X1 OR cat:X2) AND author:AAA AND ((T1 OR T2) AND NOT T3))')),
            array(array('keywords' => '{"keywords":{"$eq":"test"}}'), array('keywords' => 'test')),
        );
    }
}
