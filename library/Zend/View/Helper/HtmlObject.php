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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

use Zend\View\Exception\InvalidArgumentException;

/**
 * @uses       \Zend\View\Helper\HtmlElement
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HtmlObject extends HtmlElement
{
    /**
     * Output an object set
     *
     * @param string $data The data file
     * @param string $type Data file type
     * @param array  $attribs Attribs for the object tag
     * @param array  $params Params for in the object tag
     * @param string $content Alternative content for object
     * @return string
     * @throws InvalidArgumentException
     */
    public function __invoke($data = null, $type = null, array $attribs = array(), array $params = array(), $content = null)
    {
        if ($data == null || $type == null) {
            throw new InvalidArgumentException('HTMLObject: missing argument. $data and $type are required in htmlObject($data, $type, array $attribs = array(), array $params = array(), $content = null)');
        }

        // Merge data and type
        $attribs = array_merge(array('data' => $data,
                                     'type' => $type), $attribs);

        // Params
        $paramHtml = array();
        $closingBracket = $this->getClosingBracket();

        foreach ($params as $param => $options) {
            if (is_string($options)) {
                $options = array('value' => $options);
            }

            $options = array_merge(array('name' => $param), $options);

            $paramHtml[] = '<param' . $this->_htmlAttribs($options) . $closingBracket;
        }

        // Content
        if (is_array($content)) {
            $content = implode(self::EOL, $content);
        }

        // Object header
        $xhtml = '<object' . $this->_htmlAttribs($attribs) . '>' . self::EOL
                 . implode(self::EOL, $paramHtml) . self::EOL
                 . ($content ? $content . self::EOL : '')
                 . '</object>';

        return $xhtml;
    }
}
