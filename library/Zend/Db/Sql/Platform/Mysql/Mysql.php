<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql\Platform\Mysql;

use Zend\Db\Sql\Platform\AbstractPlatform;

/**
 * @package    Zend_Db
 * @subpackage Sql_Platform_Mysql
 */
class Mysql extends AbstractPlatform
{

    public function __construct(SelectDecorator $selectDecorator = null)
    {
        $this->setTypeDecorator('Zend\Db\Sql\Select', ($selectDecorator) ?: new SelectDecorator());
    }

}
