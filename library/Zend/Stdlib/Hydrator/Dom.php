<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator;

use DOMNode;
use DOMXPath;
use Zend\Stdlib\Exception;

class Dom extends AbstractHydrator implements HydratorOptionsInterface
{
    /**
     * @var array
     */
    protected $queryMap = array();

    /**
     * @var DOMXPath
     */
    protected $xpath;

    /**
     * Construct a new DOM hydrator
     *
     * @param  array    $queryMap = null
     * @param  DOMXPath $xpath    = null
     * @return void
     */
    public function __construct($queryMap = null, DOMXPath $xpath = null)
    {
        parent::__construct();

        if ($queryMap) {
            $this->setQueryMap($queryMap);
        }

        if ($xpath) {
            $this->setXPath($xpath);
        }
    }

    /**
     * @param  array $options
     * @return DOM
     */
    public function setOptions($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'The options parameter must be an array or a Traversable'
            );
        }

        if (isset($options['queryMap'])) {
            $this->setQueryMap($options['queryMap']);
        }

        if (isset($options["xpath"])) {
            $this->setXPath($options["xpath"]);
        }

        return $this;
    }

    /**
     * Get the query map
     *
     * @return array
     */
    public function getQueryMap()
    {
        return $this->queryMap?:array();
    }

    /**
     * Set a map of keys to XPath query strings
     *
     * @param  array $queryMap
     * @return DOM
     */
    public function setQueryMap($queryMap)
    {
        $queryMap = is_array($queryMap)?$queryMap:null;
        $this->queryMap = $queryMap;

        return $this;
    }

    /**
     * Get the XPath associated with this hydrator
     *
     * @return DOMXPath
     */
    public function getXPath()
    {
        return $this->xpath;
    }

    /**
     * Set the XPath for this hydrator
     *
     * @param  DOMXPath $xpath
     * @return DOM
     */
    public function setXPath(DOMXPath $xpath)
    {
        if(!is_null($xpath) && !($xpath instanceof DOMXPath))
            throw new Exception\InvalidArgumentException(
                "The xpath must be null or a DOMXPath"
            );

        $this->xpath = $xpath;

        return $this;
    }

    /**
     * Extract values from the given context node, using each query
     * in the query map.
     *
     * @param  DOMNode $node
     * @return array
     */
    public function extract($node)
    {
        if(!($node instanceof DOMNode))
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a DOMNode', __METHOD__
            ));

        if (!($this->xpath instanceof DOMXPath)) {
            $this->setXPath(new DOMXPath($node->ownerDocument));
        }

        $xpath = $this->getXPath();

        $attributes = array();

        foreach ($this->getQueryMap() as $attribute => $query) {
            $attributeNodes = $xpath->query($query, $node);
            if(!$attributeNodes)
                throw new \InvalidArgumentException("Malformed query");
            if ($attributeNodes->length === 1) {
                $attributeNode = $attributeNodes->item(0);
                if ($attributeNode instanceof DOMNode) {
                    $attributes[$attribute] = $attributeNode->nodeValue;
                }
            } elseif ($attributeNodes->length > 1) {
                $attributes[$attribute] = array();
                foreach ($attributeNodes as $attributeNode) {
                    if ($attributeNode instanceof DOMNode) {
                        $attributes[$attribute][] = $attributeNode->nodeValue;
                    }
                }
            }
        }

        return $attributes;
    }

    /**
     * Hydrate the given DOMNode with the input data, based on the query
     * map.
     *
     * @param  array   $data
     * @param  DOMNode $node
     * @return DOMNode
     */
    public function hydrate(array $data, $node)
    {
        if(!($node instanceof DOMNode))
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a DOMNode)', __METHOD__
            ));

        if (!($this->xpath instanceof DOMXPath)) {
            $this->setXPath(new DOMXPath($node->ownerDocument));
        }

        $xpath = $this->getXPath();

        $queryMap = $this->getQueryMap();

        foreach ($data as $attribute => $value) {
            if(!isset($queryMap[$attribute]))
                continue;

            $attributeNode = $xpath->query($queryMap[$attribute], $node)->item(0);
            if ($attributeNode instanceof DOMNode) {
                $attributeNode->nodeValue = $value;
            }
        }

        return $node;
    }
}
