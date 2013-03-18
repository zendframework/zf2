<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Model;

class ConsoleModelOptions extends AbstractModelOptions
{
    /**
     * Console output doesn't support containers
     *
     * @var string
     */
    protected $captureTo = null;

    /**
     * Error level to return after the application ends
     *
     * @var int
     */
    protected $errorLevel = 1;

    /**
     * Console output should always be terminal
     *
     * @var bool
     */
    protected $terminal = true;

    /**
     * Set error level to return after the application ends
     *
     * @param  int $errorLevel
     * @return ConsoleModelOptions
     */
    public function setErrorLevel($errorLevel)
    {
        $this->errorLevel = (int) $errorLevel;

        return $this;
    }

    /**
     * Get error level to return after the application ends
     *
     * @return int
     */
    public function getErrorLevel()
    {
        return $this->errorLevel;
    }
}
