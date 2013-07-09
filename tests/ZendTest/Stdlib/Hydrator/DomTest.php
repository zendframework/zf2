<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Zend\Stdlib\Hydrator\Dom;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\Dom}
 *
 * @covers \Zend\Stdlib\Hydrator\Dom
 * @group Zend_Stdlib
 */
class DomTest extends \PHPUnit_Framework_TestCase
{
    protected $hydrator;

    /**
     * Test extraction of a sample DOMNode
     *
     * @dataProvider extractionProvider
     */
    public function testExtraction(DOMNode $node, array $queryMap, array $values)
    {
        $this->hydrator->setQueryMap($queryMap);
        $result = $this->hydrator->extract($node);
        $this->assertEquals($values, $result);
    }

    /**
     * Test hydration of a DOMNode
     *
     * @dataProvider hydrationProvider
     */
    public function testHydration(DOMNode $node, array $queryMap, array $values)
    {
        $this->hydrator->setQueryMap($queryMap);
        $result = $this->hydrator->hydrate($values, $node);

        $xpath = $this->hydrator->getXPath();

        foreach ($queryMap as $key => $path) {
            $this->assertEquals($values[$key], $xpath->query($path, $result)->item(0)->nodeValue);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->hydrator = new Dom;
    }

    public function extractionProvider()
    {
        $document = new DOMDocument;
        $document->load(__DIR__.'/_files/DomTestNode_Extraction.xml');
        $xpath = new DOMXPath($document);

        $testNode = $xpath->query("//*")->item(0);

        $queryMap = array(
            "sampleattr" => "@sampleattr",
            "NodeOne" => "NodeOne",
            "NodeTwo" => "NodeTwo",
            "NodeThree" => "ParentNode/NodeThree"
        );

        $values = array(
            "sampleattr" => "sampleattr_value",
            "NodeOne" => "NodeOne_value",
            "NodeTwo" => "",
            "NodeThree" => "NodeThree_value"
        );

        return array(
            array(
                $testNode,
                $queryMap,
                $values
            )
        );
    }

    public function hydrationProvider()
    {
        $document = new DOMDocument;
        $document->load(__DIR__.'/_files/DomTestNode_Extraction.xml');
        $xpath = new DOMXPath($document);

        $testNode = $xpath->query("//*")->item(0);

        $queryMap = array(
            "sampleattr" => "@sampleattr",
            "NodeOne" => "NodeOne",
            "NodeTwo" => "NodeTwo",
            "NodeThree" => "ParentNode/NodeThree"
        );

        $values = array(
            "sampleattr" => "sampleattr_value",
            "NodeOne" => "NodeOne_value",
            "NodeTwo" => "",
            "NodeThree" => "NodeThree_value"
        );

        return array(
            array(
                $testNode,
                $queryMap,
                $values
            )
        );
    }
}
