<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentType;

class ContentTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testContentTypeFromStringCreatesValidContentTypeHeader()
    {
        $contentTypeHeader = ContentType::fromString('Content-Type: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentTypeHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentType', $contentTypeHeader);
    }

    public function testContentTypeGetFieldNameReturnsHeaderName()
    {
        $contentTypeHeader = new ContentType();
        $this->assertEquals('Content-Type', $contentTypeHeader->getFieldName());
    }

    public function testContentTypeGetFieldValueReturnsProperValue()
    {
        $header = ContentType::fromString('Content-Type: application/json');
        $this->assertEquals('application/json', $header->getFieldValue());
    }

    public function testContentTypeToStringReturnsHeaderFormattedString()
    {
        $header = new ContentType();
        $header->setMediaType('application/atom+xml')
               ->setCharset('ISO-8859-1');

        $this->assertEquals('Content-Type: application/atom+xml; charset=ISO-8859-1', $header->toString());
    }

    /** Implementation specific tests here */

    public function wildcardMatches()
    {
        return [
            'wildcard' => ['*/*'],
            'wildcard-format' => ['*/*+*'],
            'wildcard-type-subtype-fixed-format' => ['*/*+json'],
            'wildcard-type-partial-wildcard-subtype-fixed-format' => ['*/vnd.*+json'],
            'wildcard-type-format-subtype' => ['*/json'],
            'fixed-type-wildcard-subtype' => ['application/*'],
            'fixed-type-wildcard-subtype-fixed-format' => ['application/*+json'],
            'fixed-type-format-subtype' => ['application/json'],
            'fixed-type-fixed-subtype-wildcard-format' => ['application/vnd.foobar+*'],
            'fixed-type-partial-wildcard-subtype-fixed-format' => ['application/vnd.*+json'],
            'fixed' => ['application/vnd.foobar+json'],
            'fixed-mixed-case' => ['APPLICATION/vnd.FooBar+json'],
        ];
    }

    /**
     * @dataProvider wildcardMatches
     */
    public function testMatchWildCard($matchAgainst)
    {
        $header = ContentType::fromString('Content-Type: application/vnd.foobar+json');
        $result = $header->match($matchAgainst);
        $this->assertEquals(strtolower($matchAgainst), $result);
    }

    public function invalidMatches()
    {
        return [
            'format' => ['application/vnd.foobar+xml'],
            'wildcard-subtype' => ['application/vendor.*+json'],
            'subtype' => ['application/vendor.foobar+json'],
            'type' => ['text/vnd.foobar+json'],
            'wildcard-type-format' => ['*/vnd.foobar+xml'],
            'wildcard-type-wildcard-subtype' => ['*/vendor.*+json'],
            'wildcard-type-subtype' => ['*/vendor.foobar+json'],
        ];
    }

    /**
     * @dataProvider invalidMatches
     */
    public function testFailedMatches($matchAgainst)
    {
        $header = ContentType::fromString('Content-Type: application/vnd.foobar+json');
        $result = $header->match($matchAgainst);
        $this->assertFalse($result);
    }

    public function multipleCriteria()
    {
        $criteria = [
            'application/vnd.foobar+xml',
            'application/vnd.*+json',
            'application/vendor.foobar+xml',
            '*/vnd.foobar+json',
        ];
        return [
            'array' => [$criteria],
            'string' => [implode(',', $criteria)],
        ];
    }

    /**
     * @dataProvider multipleCriteria
     */
    public function testReturnsMatchingMediaTypeOfFirstCriteriaToValidate($criteria)
    {
        $header = ContentType::fromString('Content-Type: application/vnd.foobar+json');
        $result = $header->match($criteria);
        $this->assertEquals('application/vnd.*+json', $result);
    }

    public function contentTypeParameterExamples()
    {
        return [
            'no-quotes' => ['Content-Type: foo/bar; param=baz', 'baz'],
            'with-quotes' => ['Content-Type: foo/bar; param="baz"', 'baz'],
            'with-equals' => ['Content-Type: foo/bar; param=baz=bat', 'baz=bat'],
            'with-equals-and-quotes' => ['Content-Type: foo/bar; param="baz=bat"', 'baz=bat'],
        ];
    }

    /**
     * @dataProvider contentTypeParameterExamples
     */
    public function testContentTypeParsesParametersCorrectly($headerString, $expectedParameterValue)
    {
        $contentTypeHeader = ContentType::fromString($headerString);

        $parameters = $contentTypeHeader->getParameters();

        $this->assertArrayHasKey('param', $parameters);
        $this->assertSame($expectedParameterValue, $parameters['param']);
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = ContentType::fromString("Content-Type: foo/bar;\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new ContentType("foo/bar\r\n\r\nevilContent");
    }
}
