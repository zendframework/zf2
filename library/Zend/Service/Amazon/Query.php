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
 * @package    Zend_Service
 * @subpackage Amazon
 */

namespace Zend\Service\Amazon;
use Zend\Service\Amazon\Exception;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 */
class Query extends Amazon
{
    /**
     * Search parameters
     *
     * @var array
     */
    protected $_search = array();

    /**
     * Search index
     *
     * @var string
     */
    protected $_searchIndex = null;

    /**
     * Prepares query parameters
     *
     * @param  string $method
     * @param  array  $args
     * @throws \Zend\Service\Amazon\Exception
     * @return Zend_Service_Amazon_Query Provides a fluent interface
     */
    public function __call($method, $args)
    {
        if (strtolower($method) === 'asin') {
            $this->_searchIndex = 'asin';
            $this->_search['ItemId'] = $args[0];
            return $this;
        }

        if (strtolower($method) === 'category') {
            $this->_searchIndex = $args[0];
            $this->_search['SearchIndex'] = $args[0];
        } else if (isset($this->_search['SearchIndex']) || $this->_searchIndex !== null || $this->_searchIndex === 'asin') {
            $this->_search[$method] = $args[0];
        } else {
            throw new Exception\RuntimeException('You must set a category before setting the search parameters');
        }

        return $this;
    }

    /**
     * Search using the prepared query
     *
     * @return Zend_Service_Amazon_Item|Zend_Service_Amazon_ResultSet
     */
    public function search()
    {
        if ($this->_searchIndex === 'asin') {
            return $this->itemLookup($this->_search['ItemId'], $this->_search);
        }
        return $this->itemSearch($this->_search);
    }
}
