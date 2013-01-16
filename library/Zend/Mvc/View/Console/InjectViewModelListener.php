<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\View\Console;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\View\Http\InjectViewModelListener as HttpInjectViewModelListener;

class InjectViewModelListener extends HttpInjectViewModelListener implements ListenerAggregateInterface
{}
