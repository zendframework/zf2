<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Writer\Extension;

use DOMDocument;
use DOMElement;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage Writer_Extension
 */
interface RendererInterface
{
    /**
     * Set the data container
     *
     * @param  mixed $container
     * @return void
     */
    public function setDataContainer($container);

    /**
     * Retrieve container
     *
     * @return mixed
     */
    public function getDataContainer();

    /**
     * Set DOMDocument and DOMElement on which to operate
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $base
     * @return void
     */
    public function setDomDocument(DOMDocument $dom, DOMElement $base);

    /**
     * Render
     *
     * @return void
     */
    public function render();
}
