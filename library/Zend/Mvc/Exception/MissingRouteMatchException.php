<?php

namespace Zend\Mvc\Exception;

use Zend\Mvc\Exception,
    RuntimeException;

class MissingRouteMatchException extends RuntimeException implements Exception
{}
