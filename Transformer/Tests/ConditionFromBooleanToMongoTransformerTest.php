<?php

namespace OpenOrchestra\Transformer\Tests;

use OpenOrchestra\Transformer\ConditionFromBooleanToMongoTransformer;

/**
 * Class ConditionFromBooleanToMongoTransformerTest
 */
class ConditionFromBooleanToMongoTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConditionFromBooleanToMongoTransformer
     */
    protected $transformer;

    public function setUp()
    {
        $this->transformer = new ConditionFromBooleanToMongoTransformer('keywords');
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
            array(array('keywords' => '( NOT ( cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 OR NOT T3 )'), array('keywords' => '{"$or":[{"$and":[{"$not":{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]}},{"keywords":{"$eq":"author:AAA"}}]},{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}},{"keywords":{"$ne":"T3"}}]}]}')),
            array(array('keywords' => '( cat:X1 OR cat:X2 ) AND ( author:AAA ) AND ( T1 OR T2 OR NOT T3 )'), array('keywords' => '{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"$and":[{"keywords":{"$eq":"author:AAA"}}]},{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}},{"keywords":{"$ne":"T3"}}]}]}')),
            array(array('keywords' => 'test'), array('keywords' => '{"$and":[{"keywords":{"$eq":"test"}}]}')),
            array(array('keywords' => '( test )'), array('keywords' => '{"$and":[{"$and":[{"keywords":{"$eq":"test"}}]}]}')),
        );
    }

    /**
     * Test Exception reverseTransform
     *
     * @dataProvider provideReverseTransformException
     */
    public function testExceptionReverseTransform($value)
    {
        $this->setExpectedException('OpenOrchestra\Exceptions\MalFormedConditionException');
        $this->transformer->reverseTransform($value);
    }

    /**
     * @return array
     */
    public function provideReverseTransformException()
    {
        return array(
            array(array('keywords' => '( test(')),
            array(array('keywords' => 'NOT NOT test')),
            array(array('keywords' => 'test AND')),
            array(array('keywords' => 'test AND ')),
            array(array('keywords' => 'test0 AND AND test1')),
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
            array(array('keywords' => '{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"keywords":{"$eq":"author:AAA"}},{"$and":[{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}}]},{"keywords":{"$ne":"T3"}}]}]}'), array('keywords' => '( ( cat:X1 OR cat:X2 ) AND author:AAA AND ( ( T1 OR T2 ) AND NOT T3 ) )')),
            array(array('keywords' => '{"keywords":{"$eq":"test"}}'), array('keywords' => 'test')),
        );
    }
}
