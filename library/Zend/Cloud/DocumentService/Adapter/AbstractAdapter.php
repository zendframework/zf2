<?php
/**
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
 * @package    Zend_Cloud_DocumentService
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * namespace
 */
namespace Zend\Cloud\DocumentService\Adapter;

use Zend\Cloud\DocumentService\Document,
    Zend\Cloud\DocumentService\DocumentSet,
    Zend\Cloud\DocumentService\Query;

/**
 * Abstract document service adapter
 *
 * Provides functionality surrounding setting classes for each of:
 * - document objects
 * - document set objects
 * - query class objects
 *
 * @category   Zend
 * @package    Zend_Cloud_DocumentService
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAdapter implements AdapterInterface
{
    const DOCUMENT_CLASS    = 'document_class';
    const DOCUMENTSET_CLASS = 'documentset_class';
    const QUERY_CLASS       = 'query_class';

    /**
     * Class to utilize for new document objects
     * @var string
     */
    protected $_documentClass = 'Zend\Cloud\DocumentService\Document';

    /**
     * Class to utilize for new document set objects
     * @var string
     */
    protected $_documentSetClass = 'Zend\Cloud\DocumentService\DocumentSet';

    /**
     * Class to utilize for new query objects
     *
     * @var string
     */
    protected $_queryClass = 'Zend\Cloud\DocumentService\Query';

    /**
     * Set the class for document objects
     *
     * @param  string $class
     * @return \Zend\Cloud\DocumentService\Adapter\AbstractAdapter
     */
    public function setDocumentClass($class)
    {
        $this->_documentClass = (string) $class;
        return $this;
    }

    /**
     * Get the class for document objects
     *
     * @return string
     */
    public function getDocumentClass()
    {
        return $this->_documentClass;
    }

    /**
     * Set the class for document set objects
     *
     * @param  string $class
     * @return \Zend\Cloud\DocumentService\Adapter\AbstractAdapter
     */
    public function setDocumentSetClass($class)
    {
        $this->_documentSetClass = (string) $class;
        return $this;
    }

    /**
     * Get the class for document set objects
     *
     * @return string
     */
    public function getDocumentSetClass()
    {
        return $this->_documentSetClass;
    }

    /**
     * Set the query class for query objects
     *
     * @param  string $class
     * @return \Zend\Cloud\DocumentService\Adapter\AbstractAdapter
     */
    public function setQueryClass($class)
    {
        $this->_queryClass = (string) $class;
        return $this;
    }

    /**
     * Get the class for query objects
     *
     * @return string
     */
    public function getQueryClass()
    {
        return $this->_queryClass;
    }
}
