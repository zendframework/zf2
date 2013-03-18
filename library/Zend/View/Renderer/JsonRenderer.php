<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Renderer;

use JsonSerializable;
use Traversable;
use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Exception;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ModelInterface as Model;
use Zend\View\Renderer\JsonRendererOptions;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Resolver\ResolverInterface as Resolver;

/**
 * JSON renderer
 */
class JsonRenderer implements Renderer
{
    /**
     * Renderer options
     *
     * @var JsonRendererOptions
     */
    protected $options;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * Return the template engine object, if any
     *
     * If using a third-party template engine, such as Smarty, patTemplate,
     * phplib, etc, return the template engine object. Useful for calling
     * methods on these objects, such as for setting filters, modifiers, etc.
     *
     * @return JsonRenderer
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Set renderer options
     *
     * @param  array|\Traversable|JsonRendererOptions $options
     * @throws Exception\InvalidArgumentException
     * @return JsonRenderer
     */
    public function setOptions($options)
    {
        if (!$options instanceof JsonRendererOptions) {
            if (is_object($options) && !$options instanceof Traversable) {
                throw new Exception\InvalidArgumentException(sprintf(
                        'Expected instance of Zend\View\Renderer\JsonRendererOptions; '
                        . 'received "%s"', get_class($options))
                );
            }

            $options = new JsonRendererOptions($options);
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get renderer options
     *
     * @return JsonRendererOptions
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new JsonRendererOptions());
        }

        return $this->options;
    }

    /**
     * Set the resolver used to map a template name to a resource the renderer may consume.
     *
     * @todo   Determine use case for resolvers when rendering JSON
     * @param  Resolver $resolver
     * @return Renderer
     */
    public function setResolver(Resolver $resolver)
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * Renders values as JSON
     *
     * @todo   Determine what use case exists for accepting both $nameOrModel and $values
     * @param  string|Model $nameOrModel The script/resource process, or a view model
     * @param  null|array|\ArrayAccess $values Values to use during rendering
     * @throws Exception\DomainException
     * @return string The script output.
     */
    public function render($nameOrModel, $values = null)
    {
        // use case 1: View Models
        // Serialize variables in view model
        if ($nameOrModel instanceof Model) {
            if ($nameOrModel instanceof JsonModel) {
                $children = $this->recurseModel($nameOrModel, false);
                $this->injectChildren($nameOrModel, $children);
                $values = $nameOrModel->serialize();
            } else {
                $values = $this->recurseModel($nameOrModel);
                $values = Json::encode($values);
            }

            if ($this->getOptions()->hasJsonpCallback()) {
                $values = $this->getOptions()->getJsonpCallback() . '(' . $values . ');';
            }
            return $values;
        }

        // use case 2: $nameOrModel is populated, $values is not
        // Serialize $nameOrModel
        if (null === $values) {
            if (!is_object($nameOrModel) || $nameOrModel instanceof JsonSerializable) {
                $return = Json::encode($nameOrModel);
            } elseif ($nameOrModel instanceof Traversable) {
                $nameOrModel = ArrayUtils::iteratorToArray($nameOrModel);
                $return = Json::encode($nameOrModel);
            } else {
                $return = Json::encode(get_object_vars($nameOrModel));
            }

            if ($this->getOptions()->hasJsonpCallback()) {
                $return = $this->getOptions()->getJsonpCallback() . '(' . $return . ');';
            }
            return $return;
        }

        // use case 3: Both $nameOrModel and $values are populated
        throw new Exception\DomainException(sprintf(
            '%s: Do not know how to handle operation when both $nameOrModel and $values are populated',
            __METHOD__
        ));
    }

    /**
     * Retrieve values from a model and recurse its children to build a data structure
     *
     * @param  Model $model
     * @param  bool $mergeWithVariables Whether or not to merge children with
     *                                  the variables of the $model
     * @return array
     */
    protected function recurseModel(Model $model, $mergeWithVariables = true)
    {
        $values = array();
        if ($mergeWithVariables) {
            $values = $model->getVariables();
        }

        if ($values instanceof Traversable) {
            $values = ArrayUtils::iteratorToArray($values);
        }

        if (!$model->hasChildren()) {
            return $values;
        }

        $mergeChildren = $this->getOptions()->canMergeUnnamedChildren();
        foreach ($model as $child) {
            $captureTo = $child->getOptions()->captureTo();
            if (!$captureTo && !$mergeChildren) {
                // We don't want to do anything with this child
                continue;
            }

            $childValues = $this->recurseModel($child);
            if ($captureTo) {
                // Capturing to a specific key
                // TODO please complete if append is true. must change old
                // value to array and append to array?
                $values[$captureTo] = $childValues;
            } elseif ($mergeChildren) {
                // Merging values with parent
                $values = array_replace_recursive($values, $childValues);
            }
        }

        return $values;
    }

    /**
     * Inject discovered child model values into parent model
     *
     * @todo   detect collisions and decide whether to append and/or aggregate?
     * @param  Model $model
     * @param  array $children
     */
    protected function injectChildren(Model $model, array $children)
    {
        foreach ($children as $child => $value) {
            // TODO detect collisions and decide whether to append and/or aggregate?
            $model->setVariable($child, $value);
        }
    }
}
