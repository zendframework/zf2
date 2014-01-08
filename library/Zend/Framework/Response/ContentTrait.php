<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

trait ContentTrait
{
    /**
     * @var mixed
     */
    protected $content;

    /**
     * @return mixed
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * @param $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}
