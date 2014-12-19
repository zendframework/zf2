<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Platform;

class Sql92 extends AbstractSql92BasedPlatform
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'SQL92';
    }

    /**
     * Quote value
     *
     * @param  string $value
     * @return string
     */
    public function quoteValue($value)
    {
        trigger_error(
            'Attempting to quote a value without specific driver level support can introduce security vulnerabilities in a production environment.'
        );
        return '\'' . addcslashes($value, "\x00\n\r\\'\"\x1a") . '\'';
    }
}
