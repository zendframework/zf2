<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Reader\Extension\Podcast;
use Zend\Feed\Reader\Extension;

/**
* @category Zend
* @package Zend_Feed_Reader
*/
class Entry extends Extension\AbstractEntry
{
    /**
     * Get the entry author
     *
     * @return string
     */
    public function getCastAuthor()
    {
        if (isset($this->_data['author'])) {
            return $this->_data['author'];
        }

        $author = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:author)');

        if (!$author) {
            $author = null;
        }

        $this->_data['author'] = $author;

        return $this->_data['author'];
    }

    /**
     * Get the entry block
     *
     * @return string
     */
    public function getBlock()
    {
        if (isset($this->_data['block'])) {
            return $this->_data['block'];
        }

        $block = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:block)');

        if (!$block) {
            $block = null;
        }

        $this->_data['block'] = $block;

        return $this->_data['block'];
    }

    /**
     * Get the entry duration
     *
     * @return string
     */
    public function getDuration()
    {
        if (isset($this->_data['duration'])) {
            return $this->_data['duration'];
        }

        $duration = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:duration)');

        if (!$duration) {
            $duration = null;
        }

        $this->_data['duration'] = $duration;

        return $this->_data['duration'];
    }

    /**
     * Get the entry explicit
     *
     * @return string
     */
    public function getExplicit()
    {
        if (isset($this->_data['explicit'])) {
            return $this->_data['explicit'];
        }

        $explicit = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:explicit)');

        if (!$explicit) {
            $explicit = null;
        }

        $this->_data['explicit'] = $explicit;

        return $this->_data['explicit'];
    }

    /**
     * Get the entry keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        if (isset($this->_data['keywords'])) {
            return $this->_data['keywords'];
        }

        $keywords = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:keywords)');

        if (!$keywords) {
            $keywords = null;
        }

        $this->_data['keywords'] = $keywords;

        return $this->_data['keywords'];
    }

    /**
     * Get the entry subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        if (isset($this->_data['subtitle'])) {
            return $this->_data['subtitle'];
        }

        $subtitle = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:subtitle)');

        if (!$subtitle) {
            $subtitle = null;
        }

        $this->_data['subtitle'] = $subtitle;

        return $this->_data['subtitle'];
    }

    /**
     * Get the entry summary
     *
     * @return string
     */
    public function getSummary()
    {
        if (isset($this->_data['summary'])) {
            return $this->_data['summary'];
        }

        $summary = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:summary)');

        if (!$summary) {
            $summary = null;
        }

        $this->_data['summary'] = $summary;

        return $this->_data['summary'];
    }

    /**
     * Register iTunes namespace
     *
     */
    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
    }
}
