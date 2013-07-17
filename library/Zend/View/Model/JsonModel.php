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
use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Exception;
use Zend\View\Variables;

class JsonModel extends AbstractModel
{
    /**
     * Model options
     *
     * @var JsonModelOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param  null|array|Traversable $variables
     * @param  null|array|Traversable|JsonModelOptions $options
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
     * @param  array|\Traversable|JsonModelOptions $options
     * @throws Exception\InvalidArgumentException
     * @return JsonModel
     */
    public function setOptions($options)
    {
        if (!$options instanceof JsonModelOptions) {
            if (is_object($options) && !$options instanceof Traversable) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Expected instance of Zend\View\Model\JsonModelOptions; '
                    . 'received "%s"', get_class($options))
                );
            }

            $options = new JsonModelOptions($options);
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get model options
     *
     * @return JsonModelOptions
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new JsonModelOptions());
        }

        return $this->options;
    }

    /**
     * Serialize to JSON
     *
     * @return string
     */
    public function serialize()
    {
        $variables = $this->getVariables();
        if ($variables instanceof Traversable) {
            $variables = ArrayUtils::iteratorToArray($variables);
        }

        if (null !== $this->getOptions()->getJsonpCallback()) {
            return $this->getOptions()->getJsonpCallback().'('.Json::encode($variables).');';
        }

        return Json::encode($variables);
    }
}
