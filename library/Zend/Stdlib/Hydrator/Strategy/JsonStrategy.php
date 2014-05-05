<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Strategy;

use Zend\Json\Json;

class JsonStrategy implements StrategyInterface
{
    /**
     * @var int     Flag indicating how to decode
     */
    protected $objectDecodeType = Json::TYPE_OBJECT;

    /**
     * @var bool
     */
    protected $cycleCheck = false;

    /**
     * @var array
     */
    protected $encodeOptions = array();

    /**
     * Sets objectDecodeType
     *
     * @param  int  $objectDecodeType
     * @return self
     */
    public function setObjectDecodeType($objectDecodeType)
    {
        $this->objectDecodeType = $objectDecodeType;

        return $this;
    }

    /**
     * Sets cycleCheck
     *
     * @param  bool $cycleCheck
     * @return self
     */
    public function setCycleCheck($cycleCheck)
    {
        $this->cycleCheck = (bool) $cycleCheck;

        return $this;
    }

    /**
     * Sets encodeOptions
     *
     * @param  array $encodeOptions
     * @return self
     */
    public function setEncodeOptions(array $encodeOptions)
    {
        $this->encodeOptions = $encodeOptions;

        return $this;
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param  string $value The original encoded value.
     * @return mixed
     */
    public function hydrate($value)
    {
        return Json::decode($value, $this->objectDecodeType);
    }

    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param  mixed  $value The original value to encode.
     * @return string JSON encoded object
     */
    public function extract($value)
    {
        return Json::encode($value, $this->cycleCheck, $this->encodeOptions);
    }
}
