<?php

namespace Zend\Db\TableGateway\Exception;

use Zend\Db\Exception;

class InvalidArgumentException
    extends Exception\InvalidArgumentException
    implements ExceptionInterface
{
}