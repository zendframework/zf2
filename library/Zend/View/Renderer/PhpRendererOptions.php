<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Renderer;

use Zend\View\Exception;

class PhpRendererOptions extends AbstractRendererOptions
{
    /**
     * @var bool Whether or not to render trees of view models
     */
    protected $renderTrees = false;
}
