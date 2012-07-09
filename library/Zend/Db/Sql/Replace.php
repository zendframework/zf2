<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 */
class Replace extends Insert implements SqlInterface, PreparableSqlInterface
{
    const SPECIFICATION_INSERT = 'insert';
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';
    /**#@-*/

    /**
     * @var array Specification array
     */
    protected $specifications = array(
        self::SPECIFICATION_INSERT => 'REPLACE INTO %1$s (%2$s) VALUES (%3$s)'
    );
}
