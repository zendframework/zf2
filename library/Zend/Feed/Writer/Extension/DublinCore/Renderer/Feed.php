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
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
namespace Zend\Feed\Writer\Extension\DublinCore\Renderer;
use Zend\Feed\Writer\Extension;

/**
* @uses \Zend\Feed\Writer\Extension\AbstractRenderer
* @category Zend
* @package Zend_Feed_Writer
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
*/
class Feed extends Extension\AbstractRenderer
{

    /**
     * Set to TRUE if a rendering method actually renders something. This
     * is used to prevent premature appending of a XML namespace declaration
     * until an element which requires it is actually appended.
     *
     * @var bool
     */
    protected $_called = false;
    
    /**
     * Render feed
     * 
     * @return void
     */
    public function render()
    {
        if (strtolower($this->getType()) == 'atom') {
            return;
        }
        $this->_setAuthors($this->_dom, $this->_base);
        if ($this->_called) {
            $this->_appendNamespaces();
        }
    }
    
    /**
     * Append namespaces to feed element
     * 
     * @return void
     */
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute('xmlns:dc',
            'http://purl.org/dc/elements/1.1/');  
    }

    /**
     * Set feed authors
     * 
     * @param  DOMDocument $dom 
     * @param  DOMElement $root 
     * @return void
     */
    protected function _setAuthors(\DOMDocument $dom, \DOMElement $root)
    {
        $authors = $this->getDataContainer()->getAuthors();
        if (!$authors || empty($authors)) {
            return;
        }
        foreach ($authors as $data) {
            $author = $this->_dom->createElement('dc:creator');
            if (array_key_exists('name', $data)) {
                $text = $dom->createTextNode($data['name']);
                $author->appendChild($text);
                $root->appendChild($author);  
            }
        }
        $this->_called = true;
    }
}
