<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Model;

use Traversable;
use Zend\View\Exception;
use Zend\View\Variables;

/**
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ConsoleModel extends ViewModel
{
    const RESULT = 'result';

    /**
     * Model options
     *
     * @var ConsoleModelOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param  null|array|Traversable $variables
     * @param  null|array|Traversable|ConsoleModelOptions $options
     */
    public function __construct($variables = null, $options = null)
    {
        if (null === $variables) {
            $variables = new Variables();
        }

        // Initializing the variables container
        $this->setVariables($variables, true);

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set model options
     *
     * @param  array|\Traversable|ConsoleModelOptions $options
     * @throws Exception\InvalidArgumentException
     * @return ConsoleModel
     */
    public function setOptions($options)
    {
        if (!$options instanceof ConsoleModelOptions) {
            if (is_object($options) && !$options instanceof Traversable) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Expected instance of Zend\View\Model\ConsoleModelOptions; '
                    . 'received "%s"', get_class($options))
                );
            }

            $options = new ConsoleModelOptions($options);
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get model options
     *
     * @return ConsoleModelOptions
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new ConsoleModelOptions());
        }

        return $this->options;
    }

    /**
     * Set result text
     *
     * @param string  $text
     * @return ConsoleModel
     */
    public function setResult($text)
    {
        $this->setVariable(self::RESULT, $text);

        return $this;
    }

    /**
     * Get result text
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->getVariable(self::RESULT);
    }
}
