<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Text
 */

namespace Zend\Text\Table;

use Traversable;
use Zend\Loader\PrefixPathLoader;
use Zend\Stdlib\ArrayUtils;
use Zend\Text\Table\Decorator\DecoratorInterface as Decorator;

/**
 * Zend\Text\Table\Table enables developers to create tables out of characters
 *
 * @category  Zend
 * @package   Zend_Text_Table
 */
class Table
{
    /**
     * Auto separator settings
     */
    const AUTO_SEPARATE_NONE   = 0x0;
    const AUTO_SEPARATE_HEADER = 0x1;
    const AUTO_SEPARATE_FOOTER = 0x2;
    const AUTO_SEPARATE_ALL    = 0x4;

    /**
     * Decorator used for the table borders
     *
     * @var Decorator
     */
    protected $_decorator = null;

    /**
     * List of all column widths
     *
     * @var array
     */
    protected $_columnWidths = null;

    /**
     * Rows of the table
     *
     * @var array
     */
    protected $_rows = array();

    /**
     * Auto separation mode
     *
     * @var integer
     */
    protected $_autoSeparate = self::AUTO_SEPARATE_ALL;

    /**
     * Padding for columns
     *
     * @var integer
     */
    protected $_padding = 0;

    /**
     * Default column aligns for rows created by appendRow(array $data)
     *
     * @var array
     */
    protected $_defaultColumnAligns = array();

    /**
     * Plugin loader for decorators
     *
     * @var Zend\Loader\ShortNameLocator
     */
    protected $_pluginLoader = null;

    /**
     * Charset which is used for input by default
     *
     * @var string
     */
    protected static $_inputCharset = 'utf-8';

    /**
     * Charset which is used internally
     *
     * @var string
     */
    protected static $_outputCharset = 'utf-8';

    /**
     * Option keys to skip when calling setOptions()
     *
     * @var array
     */
    protected $_skipOptions = array(
        'options',
        'config',
        'defaultColumnAlign',
    );

    /**
     * Create a basic table object
     *
     * @param  array             $columns Widths List of all column widths
     * @param  array|Traversable $options Configuration options
     * @throws Exception\UnexpectedValueException When no columns widths were set
     */
    public function __construct($options = null)
    {
        // Set options
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (is_array($options)) {
            $this->setOptions($options);
        }

        // If no decorator was given, use default unicode decorator
        if ($this->_decorator === null) {
            if (self::getOutputCharset() === 'utf-8') {
                $this->setDecorator('unicode');
            } else {
                $this->setDecorator('ascii');
            }
        }
    }

    /**
     * Set options from array
     *
     * @param  array $options Configuration for Table
     * @return Table
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (in_array(strtolower($key), $this->_skipOptions)) {
                continue;
            }

            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Set column widths
     *
     * @param  array $columnWidths Widths of all columns
     * @throws Exception\InvalidArgumentException When no columns were supplied
     * @throws Exception\InvalidArgumentException When a column has an invalid width
     * @return Table
     */
    public function setColumnWidths(array $columnWidths)
    {
        if (count($columnWidths) === 0) {
            throw new Exception\InvalidArgumentException('You must supply at least one column');
        }

        foreach ($columnWidths as $columnNum => $columnWidth) {
            if (is_int($columnWidth) === false or $columnWidth < 1) {
                throw new Exception\InvalidArgumentException('Column ' . $columnNum . ' has an invalid'
                                                    . ' column width');
            }
        }

        $this->_columnWidths = $columnWidths;

        return $this;
    }

    /**
     * Set auto separation mode
     *
     * @param  integer $autoSeparate Auto separation mode
     * @return Table
     */
    public function setAutoSeparate($autoSeparate)
    {
        $this->_autoSeparate = (int) $autoSeparate;
        return $this;
    }

    /**
     * Set decorator
     *
     * @param  Decorator|string $decorator Decorator to use
     * @return Table
     */
    public function setDecorator($decorator)
    {
        if ($decorator instanceof Decorator) {
            $this->_decorator = $decorator;
        } else {
            $classname        = $this->getPluginLoader()->load($decorator);
            $this->_decorator = new $classname;
        }

        return $this;
    }

    /**
     * Set the column padding
     *
     * @param  integer $padding The padding for the columns
     * @return Table
     */
    public function setPadding($padding)
    {
        $this->_padding = max(0, (int) $padding);
        return $this;
    }

    /**
     * Get the plugin loader for decorators
     *
     * @return \Zend\Loader\ShortNameLocator
     */
    public function getPluginLoader()
    {
        if ($this->_pluginLoader === null) {
            $prefix     = 'Zend\Text\Table\Decorator\\';
            $pathPrefix = 'Zend/Text/Table/Decorator/';
            $this->_pluginLoader = new PrefixPathLoader(array($prefix => $pathPrefix));
        }

        return $this->_pluginLoader;
    }

    /**
     * Set default column align for rows created by appendRow(array $data)
     *
     * @param  integer $columnNum
     * @param  string  $align
     * @return Table
     */
    public function setDefaultColumnAlign($columnNum, $align)
    {
        $this->_defaultColumnAligns[$columnNum] = $align;

        return $this;
    }

    /**
     * Set the input charset for column contents
     *
     * @param string $charset
     */
    public static function setInputCharset($charset)
    {
        self::$_inputCharset = strtolower($charset);
    }

    /**
     * Get the input charset for column contents
     *
     * @param string $charset
     */
    public static function getInputCharset()
    {
        return self::$_inputCharset;
    }

    /**
     * Set the output charset for column contents
     *
     * @param string $charset
     */
    public static function setOutputCharset($charset)
    {
        self::$_outputCharset = strtolower($charset);
    }

    /**
     * Get the output charset for column contents
     *
     * @param string $charset
     */
    public static function getOutputCharset()
    {
        return self::$_outputCharset;
    }

    /**
     * Append a row to the table
     *
     * @param  array|Row $row The row to append to the table
     * @throws Exception\InvalidArgumentException When $row is neither an array nor Zend_Zext_Table_Row
     * @throws Exception\OverflowException When a row contains too many columns
     * @return Table
     */
    public function appendRow($row)
    {
        if (!is_array($row) && !($row instanceof Row)) {
            throw new Exception\InvalidArgumentException('$row must be an array or instance of Zend\Text\Table\Row');
        }

        if (is_array($row)) {
            if (count($row) > count($this->_columnWidths)) {
                throw new Exception\OverflowException('Row contains too many columns');
            }

            $data   = $row;
            $row    = new Row();
            $colNum = 0;
            foreach ($data as $columnData) {
                if (isset($this->_defaultColumnAligns[$colNum])) {
                    $align = $this->_defaultColumnAligns[$colNum];
                } else {
                    $align = null;
                }

                $row->appendColumn(new Column($columnData, $align));
                $colNum++;
            }
        }

        $this->_rows[] = $row;

        return $this;
    }

    /**
     * Render the table
     *
     * @throws Exception\UnexpectedValueException When no rows were added to the table
     * @return string
     */
    public function render()
    {
        // There should be at least one row
        if (count($this->_rows) === 0) {
            throw new Exception\UnexpectedValueException('No rows were added to the table yet');
        }

        // Initiate the result variable
        $result = '';

        // Count total columns
        $totalNumColumns = count($this->_columnWidths);

        // Check if we have a horizontal character defined
        $hasHorizontal = $this->_decorator->getHorizontal() !== '';

        // Now render all rows, starting from the first one
        $numRows = count($this->_rows);
        foreach ($this->_rows as $rowNum => $row) {
            // Get all column widths
            if (isset($columnWidths) === true) {
                $lastColumnWidths = $columnWidths;
            }

            $renderedRow  = $row->render($this->_columnWidths, $this->_decorator, $this->_padding);
            $columnWidths = $row->getColumnWidths();
            $numColumns   = count($columnWidths);

            // Check what we have to draw
            if ($rowNum === 0 && $hasHorizontal) {
                // If this is the first row, draw the table top
                $result .= $this->_decorator->getTopLeft();

                foreach ($columnWidths as $columnNum => $columnWidth) {
                    $result .= str_repeat($this->_decorator->getHorizontal(),
                                          $columnWidth);

                    if (($columnNum + 1) === $numColumns) {
                        $result .= $this->_decorator->getTopRight();
                    } else {
                        $result .= $this->_decorator->getHorizontalDown();
                    }
                }

                $result .= "\n";
            } else {
                // Else check if we have to draw the row separator
                if (!$hasHorizontal){
                    $drawSeparator = false; // there is no horizontal character;
                } elseif ($this->_autoSeparate & self::AUTO_SEPARATE_ALL) {
                    $drawSeparator = true;
                } elseif ($rowNum === 1 && $this->_autoSeparate & self::AUTO_SEPARATE_HEADER) {
                    $drawSeparator = true;
                } elseif ($rowNum === ($numRows - 1) && $this->_autoSeparate & self::AUTO_SEPARATE_FOOTER) {
                    $drawSeparator = true;
                } else {
                    $drawSeparator = false;
                }

                if ($drawSeparator) {
                    $result .= $this->_decorator->getVerticalRight();

                    $currentUpperColumn = 0;
                    $currentLowerColumn = 0;
                    $currentUpperWidth  = 0;
                    $currentLowerWidth  = 0;

                    // Add horizontal lines
                    // Loop through all column widths
                    foreach ($this->_columnWidths as $columnNum => $columnWidth) {
                        // Add the horizontal line
                        $result .= str_repeat($this->_decorator->getHorizontal(),
                                              $columnWidth);

                        // If this is the last line, break out
                        if (($columnNum + 1) === $totalNumColumns) {
                            break;
                        }

                        // Else check, which connector style has to be used
                        $connector          = 0x0;
                        $currentUpperWidth += $columnWidth;
                        $currentLowerWidth += $columnWidth;

                        if ($lastColumnWidths[$currentUpperColumn] === $currentUpperWidth) {
                            $connector          |= 0x1;
                            $currentUpperColumn += 1;
                            $currentUpperWidth   = 0;
                        } else {
                            $currentUpperWidth += 1;
                        }

                        if ($columnWidths[$currentLowerColumn] === $currentLowerWidth) {
                            $connector          |= 0x2;
                            $currentLowerColumn += 1;
                            $currentLowerWidth   = 0;
                        } else {
                            $currentLowerWidth += 1;
                        }

                        switch ($connector) {
                            case 0x0:
                                $result .= $this->_decorator->getHorizontal();
                                break;

                            case 0x1:
                                $result .= $this->_decorator->getHorizontalUp();
                                break;

                            case 0x2:
                                $result .= $this->_decorator->getHorizontalDown();
                                break;

                            case 0x3:
                                $result .= $this->_decorator->getCross();
                                break;

                            default:
                                // This can never happen, but the CS tells I have to have it ...
                                break;
                        }
                    }
                    $result .= $this->_decorator->getVerticalLeft() . "\n";
                }
            }

            // Add the rendered row to the result
            $result .= $renderedRow;

            // If this is the last row, draw the table bottom
            if (($rowNum + 1) === $numRows && $hasHorizontal) {
                $result .= $this->_decorator->getBottomLeft();

                foreach ($columnWidths as $columnNum => $columnWidth) {
                    $result .= str_repeat($this->_decorator->getHorizontal(),
                                          $columnWidth);

                    if (($columnNum + 1) === $numColumns) {
                        $result .= $this->_decorator->getBottomRight();
                    } else {
                        $result .= $this->_decorator->getHorizontalUp();
                    }
                }

                $result .= "\n";
            }
        }

        return $result;
    }

    /**
     * Magic method which returns the rendered table
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }

    }
}
