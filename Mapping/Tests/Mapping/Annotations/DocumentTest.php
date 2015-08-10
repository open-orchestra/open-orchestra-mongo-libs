<?php

namespace OpenOrchestra\Mapping\Tests\Mapping\Annotations;

use OpenOrchestra\Mapping\Annotations\Document;

/**
 * Class DocumentTest
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    protected $fakeClass;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->fakeClass = new FakeClassDocument();
    }

    /**
     * Test getSource
     */
    public function testGetSource()
    {
        $document = new Document(array('sourceField' => 'name'));
        $this->assertSame('getName', $document->getSource($this->fakeClass));
    }

    /**
     * @param array  $parameters
     * @param string $exception
     *
     * @dataProvider provideExceptionSource
     */
    public function testExceptionGetSource($parameters, $exception)
    {
        $document = new Document($parameters);
        $this->setExpectedException($exception);
        $document->getSource($this->fakeClass);
    }

    /**
     * @return array
     */
    public function provideExceptionSource()
    {
        $parameters0 = array();

        $parameters1 = array('sourceField' => 'fakeproperty');

        return array(
            array($parameters0, 'OpenOrchestra\Mapping\Exceptions\PropertyNotFoundException'),
            array($parameters1, 'OpenOrchestra\Mapping\Exceptions\MethodNotFoundException'),
        );
    }

    /**
     * Test getGenerated
     */
    public function testGetGenerated()
    {
        $document = new Document(array('generatedField' => 'fakeId'));
        $this->assertSame('getFakeId', $document->getGenerated($this->fakeClass));
    }

    /**
     * @param array  $parameters
     * @param string $exception
     *
     * @dataProvider provideExceptionGetGenerated
     */
    public function testExceptionGetGenerated($parameters, $exception)
    {
        $document = new Document($parameters);
        $this->setExpectedException($exception);
        $document->getGenerated($this->fakeClass);
    }

    /**
     * @return array
     */
    public function provideExceptionGetGenerated()
    {
        $parameters0 = array();

        $parameters1 = array('generatedField' => 'fakeproperty');

        return array(
            array($parameters0, 'OpenOrchestra\Mapping\Exceptions\PropertyNotFoundException'),
            array($parameters1, 'OpenOrchestra\Mapping\Exceptions\MethodNotFoundException'),
        );
    }

    /**
     * Test setGenerated
     */
    public function testSetGenerated()
    {
        $document = new Document(array('generatedField' => 'fakeId'));
        $this->assertSame('setFakeId', $document->setGenerated($this->fakeClass));
    }

    /**
     * @param array  $parameters
     * @param string $exception
     *
     * @dataProvider provideExceptionSetGenerated
     */
    public function testExceptionSetGenerated($parameters, $exception)
    {
        $document = new Document($parameters);
        $this->setExpectedException($exception);
        $document->setGenerated($this->fakeClass);
    }

    /**
     * @return array
     */
    public function provideExceptionSetGenerated()
    {
        $parameters0 = array();

        $parameters1 = array('generatedField' => 'fakeproperty');

        return array(
            array($parameters0, 'OpenOrchestra\Mapping\Exceptions\PropertyNotFoundException'),
            array($parameters1, 'OpenOrchestra\Mapping\Exceptions\MethodNotFoundException'),
        );
    }

    /**
     * @param array $parameters
     *
     * @dataProvider provideServiceNameAndTestMethod
     */
    public function testGetMethod($parameters)
    {
        $document = new Document($parameters);

        $this->assertSame($parameters['serviceName'], $document->getServiceName());
        $this->assertSame($parameters['testMethod'], $document->getTestMethod());
    }

    /**
     * @return array
     */
    public function provideServiceNameAndTestMethod()
    {
        return array(
            array(array('serviceName' => 'foo', 'testMethod' => 'bar')),
            array(array('serviceName' => 'bar', 'testMethod' => 'foo')),
        );
    }
}

/**
 * Class FakeClass
 */
class FakeClassDocument
{
    public function getName(){}
    public function getfakeId(){}
    public function setFakeId(){}
    public function getServiceName(){}
}
