<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter\Result;

use Serializable;
use JsonSerializable;

/**
 * The InputFilterResultInterface allow to encapsulate the result of an input collection
 */
interface InputFilterResultInterface extends Serializable, JsonSerializable
{
    /**
     * Is the validation result valid?
     *
     * @return bool
     */
    public function isValid();

    /**
     * Get the raw data (this can be a scalar or array depending of the context)
     *
     * @return mixed
     */
    public function getRawData();

    /**
     * Get the data (this can be a scalar or array depending of the context)
     *
     * @return mixed
     */
    public function getData();

    /**
     * Get error messages
     *
     * @return array
     */
    public function getErrorMessages();
}
