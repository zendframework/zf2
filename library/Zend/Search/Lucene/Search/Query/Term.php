<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Search\Lucene\Search\Query;

use Zend\Search\Lucene\Index,
    Zend\Search\Lucene,
    Zend\Search\Lucene\Search\Weight,
    Zend\Search\Lucene\Search\Highlighter\HighlighterInterface as Highlighter;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Term extends AbstractQuery
{
    /**
     * Term to find.
     *
     * @var \Zend\Search\Lucene\Index\Term
     */
    private $_term;

    /**
     * Documents vector.
     *
     * @var array
     */
    private $_docVector = null;

    /**
     * Term freqs vector.
     * array(docId => freq, ...)
     *
     * @var array
     */
    private $_termFreqs;


    /**
     * Zend_Search_Lucene_Search_Query_Term constructor
     *
     * @param \Zend\Search\Lucene\Index\Term $term
     * @param boolean $sign
     */
    public function __construct(Index\Term $term)
    {
        $this->_term = $term;
    }

    /**
     * Re-write query into primitive queries in the context of specified index
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $index
     * @return \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    public function rewrite(Lucene\SearchIndexInterface $index)
    {
        if ($this->_term->field != null) {
            return $this;
        } else {
            $query = new MultiTerm();
            $query->setBoost($this->getBoost());

            foreach ($index->getFieldNames(true) as $fieldName) {
                $term = new Index\Term($this->_term->text, $fieldName);

                $query->addTerm($term);
            }

            return $query->rewrite($index);
        }
    }

    /**
     * Optimize query in the context of specified index
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $index
     * @return \Zend\Search\Lucene\Search\Query\AbstractQuery
     */
    public function optimize(Lucene\SearchIndexInterface $index)
    {
        // Check, that index contains specified term
        if (!$index->hasTerm($this->_term)) {
            return new EmptyResult();
        }

        return $this;
    }


    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @return \Zend\Search\Lucene\Search\Weight\Weight
     */
    public function createWeight(Lucene\SearchIndexInterface $reader)
    {
        $this->_weight = new Weight\Term($this->_term, $this, $reader);
        return $this->_weight;
    }

    /**
     * Execute query in context of index reader
     * It also initializes necessary internal structures
     *
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @param \Zend\Search\Lucene\Index\DocsFilter|null $docsFilter
     */
    public function execute(Lucene\SearchIndexInterface $reader, $docsFilter = null)
    {
        $this->_docVector = array_flip($reader->termDocs($this->_term, $docsFilter));
        $this->_termFreqs = $reader->termFreqs($this->_term, $docsFilter);

        // Initialize weight if it's not done yet
        $this->_initWeight($reader);
    }

    /**
     * Get document ids likely matching the query
     *
     * It's an array with document ids as keys (performance considerations)
     *
     * @return array
     */
    public function matchedDocs()
    {
        return $this->_docVector;
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param \Zend\Search\Lucene\SearchIndexInterface $reader
     * @return float
     */
    public function score($docId, Lucene\SearchIndexInterface $reader)
    {
        if (isset($this->_docVector[$docId])) {
            return $reader->getSimilarity()->tf($this->_termFreqs[$docId]) *
                   $this->_weight->getValue() *
                   $reader->norm($docId, $this->_term->field) *
                   $this->getBoost();
        } else {
            return 0;
        }
    }

    /**
     * Return query terms
     *
     * @return array
     */
    public function getQueryTerms()
    {
        return array($this->_term);
    }

    /**
     * Return query term
     *
     * @return \Zend\Search\Lucene\Index\Term
     */
    public function getTerm()
    {
        return $this->_term;
    }

    /**
     * Query specific matches highlighting
     *
     * @param Highlighter $highlighter  Highlighter object (also contains doc for highlighting)
     */
    protected function _highlightMatches(Highlighter $highlighter)
    {
        $highlighter->highlight($this->_term->text);
    }

    /**
     * Print a query
     *
     * @return string
     */
    public function __toString()
    {
        // It's used only for query visualisation, so we don't care about characters escaping
        if ($this->_term->field !== null) {
            $query = $this->_term->field . ':';
        } else {
            $query = '';
        }

        $query .= $this->_term->text;

        if ($this->getBoost() != 1) {
            $query = $query . '^' . round($this->getBoost(), 4);
        }

        return $query;
    }
}

