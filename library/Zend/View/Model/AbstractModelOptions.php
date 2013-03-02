<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Model;

use Zend\Stdlib\AbstractOptions;

/**
 * Unless otherwise marked, all options in this class affect all adapters.
 */
abstract class AbstractModelOptions extends AbstractOptions
{
    /**
     * Is this append to child with the same capture?
     *
     * @var bool
     */
    protected $append = false;

    /**
     * What variable a parent model should capture this model to
     *
     * @var string
     */
    protected $captureTo = 'content';

    /**
     * Is this a child model?
     *
     * @var bool
     */
    protected $has_parent = false;

    /**
     * Template to use when rendering model
     *
     * @var string
     */
    protected $template = '';

    /**
     * Is this a standalone, or terminal, model?
     *
     * @var bool
     */
    protected $terminal = false;

    /**
     * Set flag indicating whether or not append to child  with the same capture
     *
     * @param  bool $flag
     * @return AbstractModelOptions
     */
    public function setAppend($flag)
    {
        $this->append = (bool) $flag;

        return $this;
    }

    /**
     * Is this append to child  with the same capture?
     *
     * @return bool
     */
    public function isAppend()
    {
        return $this->append;
    }

    /**
     * Set the name of the variable to capture this model to, if it is
     * a child model
     *
     * @param  string $capture
     * @return AbstractModelOptions
     */
    public function setCaptureTo($capture)
    {
        $this->captureTo = (string) $capture;

        return $this;
    }

    /**
     * Get the name of the variable to which to capture this model
     *
     * @return string
     */
    public function captureTo()
    {
        return $this->captureTo;
    }

    /**
     * Set flag indicating if this is a child model
     *
     * @param  bool $flag
     * @return AbstractModelOptions
     */
    public function setHasParent($flag)
    {
        $this->has_parent = (bool) $flag;

        return $this;
    }

    /**
     * Get flag indicating if this a child model
     *
     * @return bool
     */
    public function getHasParent()
    {
        return $this->has_parent;
    }

    /**
     * Set the template to be used by model
     *
     * @param  string $template
     * @return AbstractModelOptions
     */
    public function setTemplate($template)
    {
        $this->template = (string) $template;

        return $this;
    }

    /**
     * Get the template to be used by model
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set flag indicating whether or not this is considered a terminal
     * or standalone model
     *
     * @param  bool $flag
     * @return AbstractModelOptions
     */
    public function setTerminal($flag)
    {
        $this->terminal = (bool) $flag;

        return $this;
    }

    /**
     * Is this considered a terminal or standalone model?
     *
     * @return bool
     */
    public function isTerminal()
    {
        return $this->terminal;
    }
}
