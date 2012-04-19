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
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Adapter\Platform;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SqlServer implements PlatformInterface
{
    /**
     * Get name
     * 
     * @return string 
     */
    public function getName()
    {
        return 'SQLServer';
    }
    /**
     * Get quote identifier symbol
     * 
     * @return string 
     */
    public function getQuoteIdentifierSymbol()
    {
        return array('[', ']');
    }
    /**
     * Quote identifier
     * 
     * @param  string $identifier
     * @return string 
     */
    public function quoteIdentifier($identifier)
    {
        return '[' . $identifier . ']';
    }
    /**
     * Get quote value symbol
     * 
     * @return string 
     */
    public function getQuoteValueSymbol()
    {
        return '\'';
    }
    /**
     * Quote value
     * 
     * @param  string $value
     * @return string 
     */
    public function quoteValue($value)
    {
        return '\'' . str_replace('\'', '\'\'', $value) . '\'';
    }
    /**
     * Get identifier separator
     * 
     * @return string 
     */
    public function getIdentifierSeparator()
    {
        return '.';
    }
    /**
     * Quote identifier in fragment
     * 
     * @param  string $identifier
     * @param  array $safeWords
     * @return string 
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = array())
    {
        $parts = preg_split('#([\.\s])#', $identifier, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach($parts as $i => $part) {
            if ($safeWords && in_array($part, $safeWords)) {
                continue;
            }
            switch ($part) {
                case ' ':
                case '.':
                case '*':
                case 'AS':
                case 'As':
                case 'aS':
                case 'as':
                    break;
                default:
                    $parts[$i] = '[' . $part . ']';
            }
        }
        return implode('', $parts);
    }

    /**
     * Given a SQL statement, complete it with limit and order by clauses as 
     * required.
     * 
     * @param string $sql 
     * @param string $orderSql 
     * @param int    $number 
     * @param int    $offset 
     * 
     * @return string
     */
    public function limitSql($sql, $orderSql, $number, $offset)
    {
        if ($number == 0) {
            // no limit required, so just add the order's SQL to the main SQL
            $sql = $sql . ' ' . $orderSql;
        } else {
            // limit
            if ($offset == 0) {
                // Can just use TOP
                $sql = preg_replace('/^(SELECT\s(DISTINCT\s)?)/i', '$1 TOP ' . $number . ' ', $sql);
            } else {
                // we have an offset, so use the ROW_NUMBER() window function
                if (!$orderSql) {
                    // We need an order by statement
                    $orderSql = 'ORDER BY (SELECT 0)';
                }

                // remove SELECT from start of $sql as we are going to insert an additional statement
                $sql = preg_replace('/^SELECT\s/', '', $sql);

                // Build up a SELECT that uses the ROW_NUMBER() window function with
                // a sub-SELECT for the actual query we're running
                $sql = 'SELECT * FROM (SELECT ROW_NUMBER() OVER (' . $orderSql 
                    . ') AS ' . $this->quoteIdentifier('zend_db_sql_select_rownumber')
                    . ', ' . $sql . ') AS ' . $this->quoteIdentifier('zend_db_sql_select_table')
                    . ' WHERE ' . $this->quoteIdentifier('zend_db_sql_select_rownumber');

                $from = $offset + 1;
                $to = $offset + $number;
                $sql .= ' BETWEEN ' . $from . ' AND ' . $to;
            }
        }

        return trim($sql);
    }
}