<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Config
 */

namespace Zend\Config\Processor;

use Zend\Config\Config;
use Zend\Config\Exception;
use Zend\Stdlib\PriorityQueue;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Processor
 */
class Queue extends PriorityQueue implements ProcessorInterface
{
    /**
     * Process the whole config structure with each parser in the queue.
     *
     * @param  Config $config
     * @return Config
     * @throws Exception\InvalidArgumentException
     */
    public function process(Config $config)
    {
        if ($config->isReadOnly()) {
            throw new Exception\InvalidArgumentException('Cannot process config because it is read-only');
        }

        foreach ($this as $parser) {
            /** @var $parser ProcessorInterface */
            $parser->process($config);
        }
    }

    /**
     * Process a single value
     *
     * @param  mixed $value
     * @return mixed
     */
    public function processValue($value)
    {
        foreach ($this as $parser) {
            /** @var $parser ProcessorInterface */
            $value = $parser->processValue($value);
        }

        return $value;
    }
}
