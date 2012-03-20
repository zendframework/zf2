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
 * @category  Zend
 * @package   Zend_Navigation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Navigation;

use Countable,
    RecursiveIterator,
    RecursiveIteratorIterator,
    Traversable,
    Zend\Stdlib\ArrayUtils,
    Zend\Mvc\Router\Http\RouteMatch,
    Zend\View\Helper\Url as UrlHelper;

/**
 * Zend_Navigation_Container
 *
 * Container class for Zend\Navigation\Page classes.
 *
 * @category  Zend
 * @package   Zend_Navigation
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Container implements RecursiveIterator, Countable
{
    /**
     * Contains sub pages
     * @var array
     */
    protected $pages = array();

    /**
     * Index
     * An index that contains the order in which to iterate pages
     * @var array
     */
    protected $index = array();

    /**
     * Is dirty?
     * Whether index is dirty and needs to be re-arranged
     * @var bool
     */
    protected $dirtyIndex = false;

    /**
     * Url helper
     * @var Zend\View\Helper\Url
     */
    protected $urlHelper;

    /**
     * Route match
     * @var Zend\Mvc\Router\Http\RouteMatch
     */
    protected $routeMatch;


    //------------------------------------------------------------------------

    /*
     * Public API methods
     */


    /**
     * Set url helper
     * Sets injected url helper instance
     * @param Zend\View\Helper\Url $urlHelper
     * @return Container
     */
    public function setUrlHelper(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
        $this->injectIntoPages('urlHelper', $urlHelper);
        return $this;
    }


    /**
     * Set route match
     * Sets injected route match instance
     * @param \Zend\Mvc\Router\Http\RouteMatch $routeMatch
     * @return Container
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        $this->injectIntoPages('routeMatch', $routeMatch);
        return $this;
    }


    /**
     * Inject into pages
     * Recursively injects the given parameter and value into existing pages.
     * May be sed to inject routeMatch, urlHelper and any other stuff.
     * Parameter must correspond to the setter method on a page object,
     * otherwise it will be ignored.
     *
     * @param string $parameter
     * @param mixed $value
     * @return Countainer
     */
    public function injectIntoPages($parameter, $value)
    {
        foreach($this->pages as $page){

            //set parameter
            $method = 'set' . ucfirst($parameter);
            if(method_exists($page, $method)){
                $page->$method($value);
            }

            //go recursive
            $subPages = $page->getPages();
            if(!empty($subPages)) {
                $updatedSubPages = array();
                foreach($subPages as $subPage){
                    $updatedSubPages[] = $subPage->injectIntoPages(
                        $parameter,
                        $value
                    );
                }
                $page->setPages($updatedSubPages);
            }
        }

        return $this;
    }


    /**
     * Notify order updated
     * Notifies container that the order of pages are updated
     * @return void
     */
    public function notifyOrderUpdated()
    {
        $this->dirtyIndex = true;
    }


    /**
     * Set pages
     * Sets pages this container should have, removing existing pages
     *
     * @param array $pages
     * @return Container
     */
    public function setPages(array $pages)
    {
        $this->removePages();
        return $this->addPages($pages);
    }

    /**
     * Get pages
     * Returns pages in the container
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }


    /**
     * Add pages
     * Adds several pages at once
     *
     * @param  array|Traversable|Container $pages
     * @throws Exception\InvalidArgumentException
     * @return Container
     */
    public function addPages($pages)
    {
        //check pages
        if (!is_array($pages) && !$pages instanceof Traversable) {

            $error  = 'Invalid argument: $pages must be an array, an instance ';
            $error .= 'of Traversable or an instance of ';
            $error .= 'Zend\Navigation\Container.';
            throw new Exception\InvalidArgumentException($error);
        }

        // Because adding a page to a container removes it from the original
        // (see {@link Page\AbstractPage::setParent()}), iteration of the
        // original container will break. As such, we need to iterate the
        // container into an array first.
        if ($pages instanceof Container) {
            $pages = iterator_to_array($pages);
        }

        foreach ($pages as $page) {
            $this->addPage($page);
        }

        return $this;
    }


    /**
     * Add page
     * Adds a page to the container. This method will inject the container as
     * the given page's parent by calling {@link Page\AbstractPage::setParent()}
     *
     * @param  Page\AbstractPage|array|Traversable
     * @throws Exception\InvalidArgumentException
     * @return Container
     */
    public function addPage($page)
    {
        //check parent
        if ($page === $this) {
            $error = 'A page cannot have itself as a parent';
            throw new Exception\InvalidArgumentException($error);
        }

        //create page
        if (!$page instanceof Page\AbstractPage) {

            //check option
            if (!is_array($page) && !$page instanceof Traversable) {

                $error  = 'Invalid argument: $page must be an instance of ';
                $error .= 'Zend\Navigation\Page\AbstractPage or Traversable, ';
                $error .= 'or an array';
                throw new Exception\InvalidArgumentException($error);
            }

            //now create
            $page = $this->constructPage($page);
        }


        //check if page is already in container
        $hash = $page->hashCode();
        if (array_key_exists($hash, $this->index)) {
            return $this;
        }

        //adds page to container and sets dirty flag
        $this->pages[$hash] = $page;
        $this->index[$hash] = $page->getOrder();
        $this->dirtyIndex = true;

        //inject self as page parent
        $page->setParent($this);

        return $this;
    }


    /**
     * Construct page
     * Detects page type and constructs a page object of discovered type.
     * Will throw an exception if page type can not be detected.
     *
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException
     * @return AbstractPage
     */
    public function constructPage($options)
    {
        //convert traversable to array
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        //check options
        if (!is_array($options)) {
            $error = 'Error: $options must be an array or Traversable';
            throw new Exception\InvalidArgumentException($error);
        }

        //detect page type
        $pageType = $this->getPageType($options);
        if (!class_exists($pageType, true)) {
            $error = 'Cannot find class ' . $pageType;
            throw new Exception\InvalidArgumentException($error);
        }

        //inject urlHelper is exists
        if(!isset($options['urlHelper']) && $this->urlHelper) {
            $options['urlHelper'] = $this->urlHelper;
        }

        //inject routeMatch if exists
        if(!isset($options['routeMatch']) && $this->routeMatch) {
            $options['routeMatch'] = $this->routeMatch;
        }

        //instantiate page
        $page = new $pageType($options);
        if (!$page instanceof self) {
            $error  = "Invalid argument: Detected type '$pageType', which ";
            $error .= 'is not an instance of Zend\Navigation\Page\AbstractPage';
            throw new Exception\InvalidArgumentException($error);
        }

        return $page;
    }


    /**
     * Remove page
     * Removes the given page from the container. Accepts an instance of page
     * or a specific page order index. Returns boolean result.
     *
     * @param  Page\AbstractPage|int $page
     * @return bool
     */
    public function removePage($page)
    {
        if ($page instanceof Page\AbstractPage) {
            $hash = $page->hashCode();
        } elseif (is_int($page)) {
            $this->sort();
            if (!$hash = array_search($page, $this->index)) {
                return false;
            }
        } else {
            return false;
        }

        if (isset($this->pages[$hash])) {
            unset($this->pages[$hash]);
            unset($this->index[$hash]);
            $this->dirtyIndex = true;
            return true;
        }

        return false;
    }

    /**
     * Remove pages
     * Removes all pages in container
     * @return Container
     */
    public function removePages()
    {
        $this->pages = array();
        $this->index = array();
        return $this;
    }


    /**
     * Has page?
     * Checks if the container has the given page. May optionally do a
     * recursive check (false by default).
     *
     * @param Page\AbstractPage $page
     * @param bool $recursive
     * @return bool
     */
    public function hasPage(Page\AbstractPage $page, $recursive = false)
    {
        if (array_key_exists($page->hashCode(), $this->index)) {
            return true;
        } elseif ($recursive) {
            foreach ($this->pages as $childPage) {
                if ($childPage->hasPage($page, true)) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Has pages?
     * Returns true if container contains pages
     * @return bool
     */
    public function hasPages()
    {
        return count($this->index) > 0;
    }


    /**
     * Find one by
     * Returns a child page with property matching value Otherwise returns
     * null if not found
     *
     * @param string $property
     * @param  mixed  $value
     * @return Page\AbstractPage|null
     */
    public function findOneBy($property, $value)
    {
        $iterator = new RecursiveIteratorIterator(
            $this,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $page) {
            if ($page->get($property) == $value) {
                return $page;
            }
        }

        return null;
    }


    /**
     * Find all by
     * Returns all child pages with property matching value, or an empty array
     * if no pages are found.
     *
     * @param string $property
     * @param mixed $value
     * @return array
     */
    public function findAllBy($property, $value)
    {
        $result = array();

        $iterator = new RecursiveIteratorIterator(
            $this, RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $page) {
            if ($page->get($property) == $value) {
                $result[] = $page;
            }
        }

        return $result;
    }


    /**
     * Find by
     * Returns single or all pages with property matching value. By default
     * searches for a single page. Returns either a single page, an array
     * of pages or null if nothing is found.
     *
     * @param string $property
     * @param mixed $value
     * @param bool $all
     * @return Page\AbstractPage|null
     */
    public function findBy($property, $value, $all = false)
    {
        if ($all) {
            return $this->findAllBy($property, $value);
        } else {
            return $this->findOneBy($property, $value);
        }
    }


    /**
     * To array
     * Returns an array representation of all pages in container
     * @return array
     */
    public function toArray()
    {
        $this->sort();
        $pages   = array();
        $indexes = array_keys($this->index);
        foreach ($indexes as $hash) {
            $pages[] = $this->pages[$hash]->toArray();
        }
        return $pages;
    }


    /**
     * Finders overloading
     * Proxy calls to finder methods.
     *
     * Examples:
     *
     * $nav->findByLabel('foo'); // $nav->findOneBy('label', 'foo');
     * $nav->findOneByLabel('foo'); // $nav->findOneBy('label', 'foo');
     * $nav->findAllByClass('foo'); // $nav->findAllBy('class', 'foo');
     *
     * @param string $method
     * @param array $arguments
     * @throws Exception\BadMethodCallException
     */
    public function __call($method, $arguments)
    {
        if (@preg_match('/(find(?:One|All)?By)(.+)/', $method, $match)) {
            return $this->{$match[1]}($match[2], $arguments[0]);
        }

        $class = get_class($this);
        $error = 'Bad method call: Unknown method %s::%s';
        throw new Exception\BadMethodCallException(
            sprintf($error, $class, $method)
        );
    }


    //------------------------------------------------------------------------

    /*
     * Implements RecursiveIterator
     */


    /**
     * Key
     * Returns hash code of current page.
     * @return string
     */
    public function key()
    {
        $this->sort();
        return key($this->index);
    }


    /**
     * Current
     * Returns current page or null
     *
     * @throws Exception\OutOfBoundsException
     * @return Page\AbstractPage
     */
    public function current()
    {
        $this->sort();

        current($this->index);
        $hash = key($this->index);
        if (!isset($this->pages[$hash])) {
            $error  = 'Corruption detected in container ';
            $error .= 'invalid key found in internal iterator.';
            throw new Exception\OutOfBoundsException($error);
        }

        return $this->pages[$hash];
    }


    /**
     * Next
     * Moves index pointer to next page in the container.
     * @return void
     */
    public function next()
    {
        $this->sort();
        next($this->index);
    }


    /**
     * Rewind
     * Sets index pointer to first page in the container.
     * @return void
     */
    public function rewind()
    {
        $this->sort();
        reset($this->index);
    }


    /**
     * Valid
     * Checks if container index is valid.
     * @return bool
     */
    public function valid()
    {
        $this->sort();
        return current($this->index) !== false;
    }


    /**
     * Has children?
     * Proxy to hasPages to return a boolean result if container has pages.
     * @return bool  whether container has any pages
     */
    public function hasChildren()
    {
        return $this->hasPages();
    }


    /**
     * Get children
     * Returns the child container.
     * @return Page\AbstractPage|null
     */
    public function getChildren()
    {
        $hash = key($this->index);

        if (isset($this->pages[$hash])) {
            return $this->pages[$hash];
        }

        return null;
    }


    //------------------------------------------------------------------------

    /*
     * Implements Countable
     */


    /**
     * Count
     * Returns number of pages in container.
     * @return int
     */
    public function count()
    {
        return count($this->index);
    }


    //------------------------------------------------------------------------

    /*
     * Internal functionality
     */


    /**
     * Sort
     * Sorts the page index according to page order
     * @return void
     */
    protected function sort()
    {
        if (!$this->dirtyIndex) {
            return;
        }

        $newIndex = array();
        $index = 0;

        foreach ($this->pages as $hash => $page) {
            $order = $page->getOrder();
            if ($order === null) {
                $newIndex[$hash] = $index;
                $index++;
            } else {
                $newIndex[$hash] = $order;
            }
        }

        asort($newIndex);
        $this->index      = $newIndex;
        $this->dirtyIndex = false;
    }


    /**
     * Get page type
     * Returns full class name to be instantiated on success.
     *
     * Page class can be specified with 'type' key in $options.
     * The 'uri' or 'mvc' types will be resolved to corresponding page
     * classes. Any other type will be considered full class name. All
     * pages must extend Zend\Navigation\Page\AbstractPage.
     *
     * If no type is given it will be detected based on options. Options
     * containing either 'controller', 'action' or 'route' will resolve to
     * mvc page, and containing 'uri' will resolve to uri page. In all other
     * cases an exception will be thrown.
     *
     * @param  array $page
     * @throws Exception\InvalidArgumentException
     * @return AbstractPage
     */
    protected function getPageType(array $page)
    {
        //is type explicitly defined?
        if(isset($page['type']) && !empty($page['type'])) {
            $type = $page['type'];
            switch(strtolower($type)){
                case 'mvc':
                    return 'Zend\Navigation\Page\Mvc';
                    break;

                case 'uri':
                    return 'Zend\Navigation\Page\Uri';
                    break;

                default:
                    return $type;
            }
        }


        //an mvc page?
        $hasController = isset($page['controller']);
        $hasAction = isset($page['action']);
        $hasRoute = isset($page['route']);
        if($hasController || $hasAction || $hasRoute){
            return 'Zend\Navigation\Page\Mvc';
        }

        //or a uri page?
        if(isset($page['uri'])){
            return 'Zend\Navigation\Page\Uri';
        }

        //otherwise throw an exception
        $error = 'Invalid argument: Unable to determine class to instantiate';
        throw new Exception\InvalidArgumentException($error);
    }

} //class ends here
