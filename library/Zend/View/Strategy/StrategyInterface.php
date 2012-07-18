<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Strategy;

use Zend\View\Renderer\RendererInterface;

/**
 * Interface class for Zend_View compatible template engine implementations
 *
 * @category   Zend
 * @package    Zend_View
 */
interface StrategyInterface
{
    /**
     * Retrieve the composed renderer
     *
     * @return RendererInterface
     */
    public function getRenderer();

    /**
     * The match priority, normally a double between 0 and 1
     *
     * @return double
     */
    public function getMatchPriority();
}
