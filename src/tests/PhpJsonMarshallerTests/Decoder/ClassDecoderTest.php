<?php

namespace PhpJsonMarshallerTests\Decoder;

use PhpJsonMarshaller\Decoder\ClassDecoder;
use PhpJsonMarshaller\Reader\DoctrineAnnotationReader;

class ClassDecoderTest extends \PHPUnit_Framework_TestCase
{

    /** @var ClassDecoder */
    protected $decoder;

    public function setUp()
    {
        $this->decoder = new ClassDecoder(new DoctrineAnnotationReader());
    }

    /**
     * @return array
     */
    public function duplicateAnnotationExceptionProvider()
    {
        return array(
            array('\PhpJsonMarshallerTests\ExampleClass\PropertyDirectDuplicate'),
            array('\PhpJsonMarshallerTests\ExampleClass\PropertyGetterDuplicate'),
            array('\PhpJsonMarshallerTests\ExampleClass\PropertySetterDuplicate')
        );
    }

    /**
     * @return array
     */
    public function missingPropertyExceptionProvider()
    {
        return array(
            array('\PhpJsonMarshallerTests\ExampleClass\PropertyMissing'),
            array('\PhpJsonMarshallerTests\ExampleClass\MethodMissing')
        );
    }

    /**
     * @dataProvider duplicateAnnotationExceptionProvider
     * @expectedException \PhpJsonMarshaller\Exception\DuplicateAnnotationException
     * @param string $classString
     */
    public function testClassWithDuplicateProperty($classString)
    {
        $this->decoder->decodeClass($classString);
    }

    /**
     * @dataProvider missingPropertyExceptionProvider
     * @expectedException \PhpJsonMarshaller\Exception\MissingPropertyException
     * @param string $classString
     */
    public function testClassWithMissingProperty($classString)
    {
        $this->decoder->decodeClass($classString);
    }

    /**
     * @expectedException \PhpJsonMarshaller\Exception\ClassNotFoundException
     */
    public function testClassNotFoundForNonExistentClass()
    {
        $this->decoder->decodeClass('\PhpJsonMarshallerTests\ExampleClass\NonExistent');
    }

    /**
     * @throws \PhpJsonMarshaller\Exception\ClassNotFoundException
     */
    public function testClassWithNoProperty()
    {
        $result = $this->decoder->decodeClass('\PhpJsonMarshallerTests\ExampleClass\PropertyNone');
        $this->assertEquals(0, count($result->getProperties()), 'Class should have no marshall properties');
    }

    /**
     * @throws \PhpJsonMarshaller\Exception\ClassNotFoundException
     */
    public function testClassWithNoMethod()
    {
        $result = $this->decoder->decodeClass('\PhpJsonMarshallerTests\ExampleClass\MethodNone');
        $this->assertEquals(0, count($result->getProperties()), 'Class should have no marshall methods');
    }

    /**
     * @throws \PhpJsonMarshaller\Exception\ClassNotFoundException
     */
    public function testClassSuccessfulDecode()
    {
        $result = $this->decoder->decodeClass('\PhpJsonMarshallerTests\ExampleClass\ClassComplete');

        $this->assertEquals(3, count($result->getProperties()));
        $this->assertEquals(false, $result->canIgnoreUnknown());

        $this->assertEquals(true, $result->hasProperty('id'));
        $this->assertEquals(true, $result->getProperty('id')->hasJsonName());
        $this->assertEquals('id', $result->getProperty('id')->getJsonName());
        $this->assertEquals(true, $result->getProperty('id')->hasJsonType());
        $this->assertEquals('int', $result->getProperty('id')->getJsonType());
        $this->assertEquals('id', $result->getProperty('id')->getDirect());
        $this->assertEquals(true, $result->getProperty('id')->hasDirect());
        $this->assertEquals(false, $result->getProperty('id')->hasGetter());
        $this->assertEquals(false, $result->getProperty('id')->hasSetter());

        $this->assertEquals(true, $result->hasProperty('active'));
        $this->assertEquals(true, $result->getProperty('active')->hasJsonName());
        $this->assertEquals('active', $result->getProperty('active')->getJsonName());
        $this->assertEquals(true, $result->getProperty('active')->hasJsonType());
        $this->assertEquals('boolean', $result->getProperty('active')->getJsonType());
        $this->assertEquals(false, $result->getProperty('active')->hasDirect());
        $this->assertEquals(true, $result->getProperty('active')->hasGetter());
        $this->assertEquals(true, $result->getProperty('active')->hasSetter());
        $this->assertEquals('setActive', $result->getProperty('active')->getSetter());
        $this->assertEquals('isActive', $result->getProperty('active')->getGetter());

        $this->assertEquals(true, $result->hasProperty('name'));
        $this->assertEquals(true, $result->getProperty('name')->hasJsonName());
        $this->assertEquals('name', $result->getProperty('name')->getJsonName());
        $this->assertEquals(true, $result->getProperty('name')->hasJsonType());
        $this->assertEquals('string', $result->getProperty('name')->getJsonType());
        $this->assertEquals(false, $result->getProperty('name')->hasDirect());
        $this->assertEquals(true, $result->getProperty('name')->hasGetter());
        $this->assertEquals(true, $result->getProperty('name')->hasSetter());
        $this->assertEquals('getName', $result->getProperty('name')->getGetter());
        $this->assertEquals('setName', $result->getProperty('name')->getSetter());
    }


}
