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
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\InternalType;
use Zend\Pdf\Exception;
use Zend\Pdf\ObjectFactory;
use Zend\Pdf;

/**
 * PDF file 'indirect object' element implementation
 *
 * @uses       \Zend\Pdf\InternalType\AbstractTypeObject
 * @uses       \Zend\Pdf\ObjectFactory
 * @uses       \Zend\Pdf\Exception
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IndirectObject extends AbstractTypeObject
{
    /**
     * Object value
     *
     * @var \Zend\Pdf\InternalType\AbstractTypeObject
     */
    protected $_value;

    /**
     * Object number within PDF file
     *
     * @var integer
     */
    protected $_objNum;

    /**
     * Generation number
     *
     * @var integer
     */
    protected $_genNum;

    /**
     * Reference to the factory.
     *
     * @var \Zend\Pdf\ObjectFactory
     */
    protected $_factory;

    /**
     * Object constructor
     *
     * @param \Zend\Pdf\InternalType\AbstractTypeObject $val
     * @param integer $objNum
     * @param integer $genNum
     * @param \Zend\Pdf\ObjectFactory $factory
     * @throws \Zend\Pdf\Exception
     */
    public function __construct(AbstractTypeObject $val,
                                $objNum,
                                $genNum,
                                ObjectFactory $factory)
    {
        if ($val instanceof self) {
            throw new Exception\RuntimeException('Object number must not be an instance of \Zend\Pdf\InternalType\IndirectObject.');
        }

        if ( !(is_integer($objNum) && $objNum > 0) ) {
            throw new Exception\RuntimeException('Object number must be positive integer.');
        }

        if ( !(is_integer($genNum) && $genNum >= 0) ) {
            throw new Exception\RuntimeException('Generation number must be non-negative integer.');
        }

        $this->_value   = $val;
        $this->_objNum  = $objNum;
        $this->_genNum  = $genNum;
        $this->_factory = $factory;

        $this->setParentObject($this);

        $factory->registerObject($this, $objNum . ' ' . $genNum);
    }


    /**
     * Check, that object is generated by specified factory
     *
     * @return \Zend\Pdf\ObjectFactory
     */
    public function getFactory()
    {
        return $this->_factory;
    }

    /**
     * Return type of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->_value->getType();
    }


    /**
     * Get object number
     *
     * @return integer
     */
    public function getObjNum()
    {
        return $this->_objNum;
    }


    /**
     * Get generation number
     *
     * @return integer
     */
    public function getGenNum()
    {
        return $this->_genNum;
    }


    /**
     * Return reference to the object
     *
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return string
     */
    public function toString(Pdf\ObjectFactory $factory = null)
    {
        if ($factory === null) {
            $shift = 0;
        } else {
            $shift = $factory->getEnumerationShift($this->_factory);
        }

        return $this->_objNum + $shift . ' ' . $this->_genNum . ' R';
    }


    /**
     * Dump object to a string to save within PDF file.
     *
     * $factory parameter defines operation context.
     *
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return string
     */
    public function dump(ObjectFactory $factory)
    {
        $shift = $factory->getEnumerationShift($this->_factory);

        return  $this->_objNum + $shift . " " . $this->_genNum . " obj \n"
             .  $this->_value->toString($factory) . "\n"
             . "endobj\n";
    }

    /**
     * Get handler
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->_value->$property;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($property, $value)
    {
        $this->_value->$property = $value;
    }

    /**
     * Call handler
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->_value, $method), $args);
    }

    /**
     * Detach PDF object from the factory (if applicable), clone it and attach to new factory.
     *
     * @param \Zend\Pdf\ObjectFactory $factory  The factory to attach
     * @param array &$processed  List of already processed indirect objects, used to avoid objects duplication
     * @param integer $mode  Cloning mode (defines filter for objects cloning)
     * @returns \Zend\Pdf\InternalType\AbstractTypeObject
     */
    public function makeClone(ObjectFactory $factory, array &$processed, $mode)
    {
        $id = spl_object_hash($this);
        if (isset($processed[$id])) {
            // Do nothing if object is already processed
            // return it
            return $processed[$id];
        }

        // Create obect with null value and register it in $processed container
        $processed[$id] = $clonedObject = $factory->newObject(new NullObject());

        // Pecursively process actual data
        $clonedObject->_value = $this->_value->makeClone($factory, $processed, $mode);

        if ($clonedObject->_value instanceof NullObject) {
            // Do not store null objects within $processed container since it may be filtered
            // by $mode parameter but used in some future pass
            unset($processed[$id]);

            // Return direct null object
            return $clonedObject->_value;
        }

        return $clonedObject;
    }

    /**
     * Mark object as modified, to include it into new PDF file segment
     */
    public function touch()
    {
        $this->_factory->markAsModified($this);
    }

    /**
     * Return object, which can be used to identify object and its references identity
     *
     * @return \Zend\Pdf\InternalType\IndirectObject
     */
    public function getObject()
    {
        return $this;
    }

    /**
     * Clean up resources, used by object
     */
    public function cleanUp()
    {
        $this->_value = null;
    }

    /**
     * Convert PDF element to PHP type.
     *
     * @return mixed
     */
    public function toPhp()
    {
        return $this->_value->toPhp();
    }
}
