<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

interface ValidatorInterface
{
    /**
     * Validate data and its optional context and create a validation result
     *
     * @param  mixed $data
     * @param  mixed|null $context
     * @return Result\ValidationResultInterface
     */
    public function validate($data, $context = null);

    /**
     * Proxy to validate method (this allows to make any validator callable)
     *
     * @param  mixed $data
     * @param  mixed|null $context
     * @return Result\ValidationResultInterface
     */
    public function __invoke($data, $context = null);
}
