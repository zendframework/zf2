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
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml_Security
 */

namespace Zend\InfoCard\XML\Security\Transform;

use Zend\InfoCard\XML\Security\Transform;
use Zend\InfoCard\XML\Security\Exception;

/**
 * A Transform to perform C14n XML Exclusive Canonicalization
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml_Security
 */
class XMLExcC14N implements TransformInterface
{
    /**
     * Transform the input XML based on C14n XML Exclusive Canonicalization rules
     *
     * @throws Exception\RuntimeException
     * @param string $strXMLData The input XML
     * @return string The output XML
     */
    public function transform($strXMLData)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($strXMLData);

        if(method_exists($dom, 'C14N')) {
            return $dom->C14N(true, false);
        }

        throw new Exception\RuntimeException("This transform requires the C14N() method to exist in the DOM extension");
    }
}
