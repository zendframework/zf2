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
 * @package    Reader\Reader
 */

namespace Zend\Feed\Reader\Entry;

use DateTime;
use DOMElement;
use DOMXPath;
use Zend\Feed\Reader;
use Zend\Feed\Reader\Exception;

/**
* @category Zend
* @package Reader\Reader
*/
class Rss extends AbstractEntry implements EntryInterface
{

    /**
     * XPath query for RDF
     *
     * @var string
     */
    protected $_xpathQueryRdf = '';

    /**
     * XPath query for RSS
     *
     * @var string
     */
    protected $_xpathQueryRss = '';

    /**
     * Constructor
     *
     * @param  DOMElement $entry
     * @param  string $entryKey
     * @param  string $type
     * @return void
     */
    public function __construct(DOMElement $entry, $entryKey, $type = null)
    {
        parent::__construct($entry, $entryKey, $type);
        $this->_xpathQueryRss = '//item[' . ($this->_entryKey+1) . ']';
        $this->_xpathQueryRdf = '//rss:item[' . ($this->_entryKey+1) . ']';

        $pluginLoader = Reader\Reader::getPluginLoader();

        $dublinCoreClass = $pluginLoader->getClassName('DublinCore\\Entry');
        $this->_extensions['DublinCore\\Entry'] = new $dublinCoreClass($entry, $entryKey, $type);

        $contentClass   = $pluginLoader->getClassName('Content\\Entry');
        $this->_extensions['Content\\Entry'] = new $contentClass($entry, $entryKey, $type);

        $atomClass   = $pluginLoader->getClassName('Atom\\Entry');
        $this->_extensions['Atom\\Entry'] = new $atomClass($entry, $entryKey, $type);

        $wfwClass   = $pluginLoader->getClassName('WellFormedWeb\\Entry');
        $this->_extensions['WellFormedWeb\\Entry'] = new $wfwClass($entry, $entryKey, $type);

        $slashClass   = $pluginLoader->getClassName('Slash\\Entry');
        $this->_extensions['Slash\\Entry'] = new $slashClass($entry, $entryKey, $type);

        $threadClass   = $pluginLoader->getClassName('Thread\\Entry');
        $this->_extensions['Thread\\Entry'] = new $threadClass($entry, $entryKey, $type);
    }

    /**
     * Get an author entry
     *
     * @param DOMElement $element
     * @return string
     */
    public function getAuthor($index = 0)
    {
        $authors = $this->getAuthors();

        if (isset($authors[$index])) {
            return $authors[$index];
        }

        return null;
    }

    /**
     * Get an array with feed authors
     *
     * @return array
     */
    public function getAuthors()
    {
        if (array_key_exists('authors', $this->_data)) {
            return $this->_data['authors'];
        }
        
        $authors = array();
        $authors_dc = $this->getExtension('DublinCore')->getAuthors();
        if (!empty($authors_dc)) {
            foreach ($authors_dc as $author) {
                $authors[] = array(
                    'name' => $author['name']
                );
            }
        }
        
        if ($this->getType() !== Reader\Reader::TYPE_RSS_10
        && $this->getType() !== Reader\Reader::TYPE_RSS_090) {
            $list = $this->_xpath->query($this->_xpathQueryRss . '//author');
        } else {
            $list = $this->_xpath->query($this->_xpathQueryRdf . '//rss:author');
        }
        if ($list->length) {
            foreach ($list as $author) {
                $string = trim($author->nodeValue);
                $email = null;
                $name = null;
                $data = array();
                // Pretty rough parsing - but it's a catchall
                if (preg_match("/^.*@[^ ]*/", $string, $matches)) {
                    $data['email'] = trim($matches[0]);
                    if (preg_match("/\((.*)\)$/", $string, $matches)) {
                        $data['name'] = $matches[1];
                    }
                    $authors[] = $data;
                } 
            }
        }

        if (count($authors) == 0) {
            $authors = $this->getExtension('Atom')->getAuthors();
        } else {
            $authors = new Reader\Collection\Author(
                Reader\Reader::arrayUnique($authors)
            );
        }

        if (count($authors) == 0) {
            $authors = null;
        }

        $this->_data['authors'] = $authors;

        return $this->_data['authors'];
    }

    /**
     * Get the entry content
     *
     * @return string
     */
    public function getContent()
    {
        if (array_key_exists('content', $this->_data)) {
            return $this->_data['content'];
        }

        $content = $this->getExtension('Content')->getContent();

        if (!$content) {
            $content = $this->getDescription();
        }

        if (empty($content)) {
            $content = $this->getExtension('Atom')->getContent();
        }

        $this->_data['content'] = $content;

        return $this->_data['content'];
    }

    /**
     * Get the entry's date of creation
     *
     * @return string
     */
    public function getDateCreated()
    {
        return $this->getDateModified();
    }

    /**
     * Get the entry's date of modification
     *
     * @return string
     */
    public function getDateModified()
    {
        if (array_key_exists('datemodified', $this->_data)) {
            return $this->_data['datemodified'];
        }

        $dateModified = null;
        $date = null;

        if ($this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $dateModified = $this->_xpath->evaluate('string('.$this->_xpathQueryRss.'/pubDate)');
            if ($dateModified) {
                $dateModifiedParsed = strtotime($dateModified);
                if ($dateModifiedParsed) {
                    $date = new DateTime('@' . $dateModifiedParsed);
                } else {
                    $dateStandards = array(DateTime::RSS, DateTime::RFC822,
                                           DateTime::RFC2822, null);
                    foreach ($dateStandards as $standard) {
                        try {
                            $date = date_create_from_format($standard, $dateModified);
                            break;
                        } catch (\Exception $e) {
                            if ($standard == null) {
                                throw new Exception\RuntimeException(
                                    'Could not load date due to unrecognised'
                                    .' format (should follow RFC 822 or 2822):'
                                    . $e->getMessage(),
                                    0, $e
                                );
                            }
                        }
                    }
                }
            }
        }

        if (!$date) {
            $date = $this->getExtension('DublinCore')->getDate();
        }

        if (!$date) {
            $date = $this->getExtension('Atom')->getDateModified();
        }

        if (!$date) {
            $date = null;
        }

        $this->_data['datemodified'] = $date;

        return $this->_data['datemodified'];
    }

    /**
     * Get the entry description
     *
     * @return string
     */
    public function getDescription()
    {
        if (array_key_exists('description', $this->_data)) {
            return $this->_data['description'];
        }

        $description = null;

        if ($this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $description = $this->_xpath->evaluate('string('.$this->_xpathQueryRss.'/description)');
        } else {
            $description = $this->_xpath->evaluate('string('.$this->_xpathQueryRdf.'/rss:description)');
        }

        if (!$description) {
            $description = $this->getExtension('DublinCore')->getDescription();
        }

        if (empty($description)) {
            $description = $this->getExtension('Atom')->getDescription();
        }

        if (!$description) {
            $description = null;
        }

        $this->_data['description'] = $description;

        return $this->_data['description'];
    }

    /**
     * Get the entry enclosure
     * @return string
     */
    public function getEnclosure()
    {
        if (array_key_exists('enclosure', $this->_data)) {
            return $this->_data['enclosure'];
        }

        $enclosure = null;

        if ($this->getType() == Reader\Reader::TYPE_RSS_20) {
            $nodeList = $this->_xpath->query($this->_xpathQueryRss . '/enclosure');

            if ($nodeList->length > 0) {
                $enclosure = new \stdClass();
                $enclosure->url    = $nodeList->item(0)->getAttribute('url');
                $enclosure->length = $nodeList->item(0)->getAttribute('length');
                $enclosure->type   = $nodeList->item(0)->getAttribute('type');
            }
        }

        if (!$enclosure) {
            $enclosure = $this->getExtension('Atom')->getEnclosure();
        }

        $this->_data['enclosure'] = $enclosure;

        return $this->_data['enclosure'];
    }

    /**
     * Get the entry ID
     *
     * @return string
     */
    public function getId()
    {
        if (array_key_exists('id', $this->_data)) {
            return $this->_data['id'];
        }

        $id = null;

        if ($this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $id = $this->_xpath->evaluate('string('.$this->_xpathQueryRss.'/guid)');
        }

        if (!$id) {
            $id = $this->getExtension('DublinCore')->getId();
        }

        if (empty($id)) {
            $id = $this->getExtension('Atom')->getId();
        }

        if (!$id) {
            if ($this->getPermalink()) {
                $id = $this->getPermalink();
            } elseif ($this->getTitle()) {
                $id = $this->getTitle();
            } else {
                $id = null;
            }
        }

        $this->_data['id'] = $id;

        return $this->_data['id'];
    }

    /**
     * Get a specific link
     *
     * @param  int $index
     * @return string
     */
    public function getLink($index = 0)
    {
        if (!array_key_exists('links', $this->_data)) {
            $this->getLinks();
        }

        if (isset($this->_data['links'][$index])) {
            return $this->_data['links'][$index];
        }

        return null;
    }

    /**
     * Get all links
     *
     * @return array
     */
    public function getLinks()
    {
        if (array_key_exists('links', $this->_data)) {
            return $this->_data['links'];
        }

        $links = array();

        if ($this->getType() !== Reader\Reader::TYPE_RSS_10 &&
            $this->getType() !== Reader\Reader::TYPE_RSS_090) {
            $list = $this->_xpath->query($this->_xpathQueryRss.'//link');
        } else {
            $list = $this->_xpath->query($this->_xpathQueryRdf.'//rss:link');
        }

        if (!$list->length) {
            $links = $this->getExtension('Atom')->getLinks();
        } else {
            foreach ($list as $link) {
                $links[] = $link->nodeValue;
            }
        }

        $this->_data['links'] = $links;

        return $this->_data['links'];
    }
    
    /**
     * Get all categories
     *
     * @return Reader\Collection\Category
     */
    public function getCategories()
    {
        if (array_key_exists('categories', $this->_data)) {
            return $this->_data['categories'];
        }

        if ($this->getType() !== Reader\Reader::TYPE_RSS_10 &&
            $this->getType() !== Reader\Reader::TYPE_RSS_090) {
            $list = $this->_xpath->query($this->_xpathQueryRss.'//category');
        } else {
            $list = $this->_xpath->query($this->_xpathQueryRdf.'//rss:category');
        }

        if ($list->length) {
            $categoryCollection = new Reader\Collection\Category;
            foreach ($list as $category) {
                $categoryCollection[] = array(
                    'term' => $category->nodeValue,
                    'scheme' => $category->getAttribute('domain'),
                    'label' => $category->nodeValue,
                );
            }
        } else {
            $categoryCollection = $this->getExtension('DublinCore')->getCategories();
        }
        
        if (count($categoryCollection) == 0) {
            $categoryCollection = $this->getExtension('Atom')->getCategories();
        }

        $this->_data['categories'] = $categoryCollection;

        return $this->_data['categories'];
    }

    /**
     * Get a permalink to the entry
     *
     * @return string
     */
    public function getPermalink()
    {
        return $this->getLink(0);
    }

    /**
     * Get the entry title
     *
     * @return string
     */
    public function getTitle()
    {
        if (array_key_exists('title', $this->_data)) {
            return $this->_data['title'];
        }

        $title = null;

        if ($this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $title = $this->_xpath->evaluate('string('.$this->_xpathQueryRss.'/title)');
        } else {
            $title = $this->_xpath->evaluate('string('.$this->_xpathQueryRdf.'/rss:title)');
        }

        if (!$title) {
            $title = $this->getExtension('DublinCore')->getTitle();
        }

        if (!$title) {
            $title = $this->getExtension('Atom')->getTitle();
        }

        if (!$title) {
            $title = null;
        }

        $this->_data['title'] = $title;

        return $this->_data['title'];
    }

    /**
     * Get the number of comments/replies for current entry
     *
     * @return string|null
     */
    public function getCommentCount()
    {
        if (array_key_exists('commentcount', $this->_data)) {
            return $this->_data['commentcount'];
        }

        $commentcount = $this->getExtension('Slash')->getCommentCount();

        if (!$commentcount) {
            $commentcount = $this->getExtension('Thread')->getCommentCount();
        }

        if (!$commentcount) {
            $commentcount = $this->getExtension('Atom')->getCommentCount();
        }

        if (!$commentcount) {
            $commentcount = null;
        }

        $this->_data['commentcount'] = $commentcount;

        return $this->_data['commentcount'];
    }

    /**
     * Returns a URI pointing to the HTML page where comments can be made on this entry
     *
     * @return string
     */
    public function getCommentLink()
    {
        if (array_key_exists('commentlink', $this->_data)) {
            return $this->_data['commentlink'];
        }

        $commentlink = null;

        if ($this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $commentlink = $this->_xpath->evaluate('string('.$this->_xpathQueryRss.'/comments)');
        }

        if (!$commentlink) {
            $commentlink = $this->getExtension('Atom')->getCommentLink();
        }

        if (!$commentlink) {
            $commentlink = null;
        }

        $this->_data['commentlink'] = $commentlink;

        return $this->_data['commentlink'];
    }

    /**
     * Returns a URI pointing to a feed of all comments for this entry
     *
     * @return string
     */
    public function getCommentFeedLink()
    {
        if (array_key_exists('commentfeedlink', $this->_data)) {
            return $this->_data['commentfeedlink'];
        }

        $commentfeedlink = $this->getExtension('WellFormedWeb')->getCommentFeedLink();

        if (!$commentfeedlink) {
            $commentfeedlink = $this->getExtension('Atom')->getCommentFeedLink('rss');
        }

        if (!$commentfeedlink) {
            $commentfeedlink = $this->getExtension('Atom')->getCommentFeedLink('rdf');
        }

        if (!$commentfeedlink) {
            $commentfeedlink = null;
        }

        $this->_data['commentfeedlink'] = $commentfeedlink;

        return $this->_data['commentfeedlink'];
    }

    /**
     * Set the XPath query (incl. on all Extensions)
     *
     * @param DOMXPath $xpath
     * @return void
     */
    public function setXpath(DOMXPath $xpath)
    {
        parent::setXpath($xpath);
        foreach ($this->_extensions as $extension) {
            $extension->setXpath($this->_xpath);
        }
    }
}
