<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql\Platform\SqlServer;

use Zend\Db\Sql\Platform\AbstractPlatform;

class SqlServer extends AbstractPlatform
{

    public function __construct(SelectDecorator $selectDecorator = null)
    {
        $this->setTypeDecorator('Zend\Db\Sql\Select', ($selectDecorator) ?: new SelectDecorator());
    }

}
