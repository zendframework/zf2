<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper;

use Zend\View\Exception\InvalidArgumentException;

/**
 * @package    Zend_View
 * @subpackage Helper
 */
class HtmlObject extends AbstractHtmlElement
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

            $paramHtml[] = '<param' . $this->htmlAttribs($options) . $closingBracket;
        }

        // Content
        if (is_array($content)) {
            $content = implode(self::EOL, $content);
        }

        // Object header
        $xhtml = '<object' . $this->htmlAttribs($attribs) . '>' . self::EOL
                 . implode(self::EOL, $paramHtml) . self::EOL
                 . ($content ? $content . self::EOL : '')
                 . '</object>';

        return $xhtml;
    }
}
