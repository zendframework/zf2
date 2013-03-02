<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Model;

use Traversable;
use Zend\View\Exception;
use Zend\View\Variables;

class ViewModel extends AbstractModel
{
    /**
     * Model options
     *
     * @var ViewModelOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param  null|array|Traversable $variables
     * @param  null|array|Traversable|ViewModelOptions $options
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
     * @param  array|\Traversable|ViewModelOptions $options
     * @throws Exception\InvalidArgumentException
     * @return ViewModel
     */
    public function setOptions($options)
    {
        if (!$options instanceof ViewModelOptions) {
            if (is_object($options) && !$options instanceof Traversable) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Expected instance of Zend\View\Model\ViewModelOptions; '
                    . 'received "%s"', get_class($options))
                );
            }

            $options = new ViewModelOptions($options);
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get model options
     *
     * @return ViewModelOptions
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new ViewModelOptions());
        }

        return $this->options;
    }
}
