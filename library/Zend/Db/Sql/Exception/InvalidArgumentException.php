<?php

namespace Zend\Db\Sql\Exception;

use Zend\Db\Exception;

class InvalidArgumentException
    extends Exception\InvalidArgumentException
    implements ExceptionInterface
{
}