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
 * Helper for creating tables
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class HtmlTable extends AbstractHtmlElement
{
    /**
     * Generates a 'Table' element.
     *
     * @param array   $headerCells array of cell values for the th elements
     * @param array   $rowCells array of values for each row. one array per row
     * @param array   $attribs Attributes for the table tag.
     * @param bool    $escape Escape the cell values
     * @return string The list XHTML.
     */
    public function __invoke(array $headerCells, array $rowCells, $attribs = false, $escape = true)
    {
        // TODO allow a cycle to be passed in for striping?
        // TODO allow class names for each column for styling? - via keyname set in headerCells?
        // TODO allow class names for each row?

        // few basic data checks
        if (count($rowCells) == 0){
            throw new InvalidArgumentException('At least one row must be specified');
        }
        $count = count($rowCells[0]);
        foreach ($rowCells as $rc){
            if ($count != count($rc)){
                throw new InvalidArgumentException('Each data row must have the same amount of cells');
            }
        }
        if (count($headerCells) != count($rowCells[0])){
            throw new InvalidArgumentException('There must be the same amount of columns in the data array as the header array');
        }

        if ($escape) {
            $escaper = $this->view->plugin('escapeHtml');
        }

        $headerRow = '<tr>';
        foreach ($headerCells as $cell){
            $value = $escape ? $escaper($cell) : $cell;
            $headerRow .= '<th>' . $value . '</th>';
        }
        $headerRow .= '</tr>';

        $dataRows = '';
        foreach ($rowCells as $row){
            $dataRows .= '<tr>';
            foreach ($row as $cell){
                $value = $escape ? $escaper($cell) : $cell;
                $dataRows .= '<td>' . $value . '</td>';
            }
            $dataRows .= '</tr>';
        }

        if ($attribs) {
            $attribs = $this->htmlAttribs($attribs);
        } else {
            $attribs = '';
        }

        return '<table' . $attribs . '>' . self::EOL . $headerRow . self::EOL . $dataRows . '</table>' . self::EOL;
    }
}
