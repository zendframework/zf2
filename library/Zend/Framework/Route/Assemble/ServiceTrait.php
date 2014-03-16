<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Assemble;

trait ServiceTrait
{
    /**
     * @var AssemblerInterface
     */
    protected $assembler;

    /**
     * @param array $params
     * @param array $options
     * @return mixed
     */
    public function url(array $params = [], array $options = [])
    {
        return $this->assembler->url($params, $options);
    }

    /**
     * @param  AssemblerInterface $assembler
     * @return self
     */
    public function setAssembler(AssemblerInterface $assembler)
    {
        $this->assembler = $assembler;
        return $this;
    }

    /**
     * @return AssemblerInterface
     */
    public function assembler()
    {
        return $this->assembler;
    }
}
