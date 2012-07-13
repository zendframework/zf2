<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Analytics\Extension;

use Zend\GData;

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Analytics
 */
class Goal extends GData\Extension
{
    protected $_rootNamespace = 'ga';
    protected $_rootElement = 'goal';

    public function __construct()
    {
        $this->registerAllNamespaces(GData\Analytics::$namespaces);
        parent::__construct();
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        $attribs = $this->getExtensionAttributes();
        return $attribs['name']['value'];
    }
}
