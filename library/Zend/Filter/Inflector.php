<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

/**
 * Filter chain for string inflection
 */
class Inflector extends AbstractFilter
{
    /**
     * @var FilterPluginManager
     */
    protected $filterPluginManager;

    /**
     * @var bool
     */
    protected $throwTargetExceptionsOn = true;

    /**
     * @var string
     */
    protected $targetReplacementIdentifier = ':';

    /**
     * @var string
     */
    protected $target = '';

    /**
     * @var array
     */
    protected $rules = array();

    /**
     * Constructor
     *
     * @param FilterPluginManager $filterPluginManager
     * @param array               $options Options to set
     */
    public function __construct(FilterPluginManager $filterPluginManager, array $options = array())
    {
        $this->filterPluginManager = $filterPluginManager;
        parent::__construct($options);
    }

    /**
     * Retrieve plugin manager
     *
     * @return FilterPluginManager
     */
    public function getPluginManager()
    {
        return $this->filterPluginManager;
    }

    /**
     * Set whether or not the inflector should throw an exception when a replacement
     * identifier is still found within an inflected target.
     *
     * @param bool $throwTargetExceptionsOn
     * @return void
     */
    public function setThrowTargetExceptionsOn($throwTargetExceptionsOn)
    {
        $this->throwTargetExceptionsOn = (bool) $throwTargetExceptionsOn;
    }

    /**
     * Will exceptions be thrown?
     *
     * @return bool
     */
    public function isThrowTargetExceptionsOn()
    {
        return $this->throwTargetExceptionsOn;
    }

    /**
     * Set the Target Replacement Identifier, by default ':'
     *
     * @param string $targetReplacementIdentifier
     * @return void
     */
    public function setTargetReplacementIdentifier($targetReplacementIdentifier)
    {
        $this->targetReplacementIdentifier = (string) $targetReplacementIdentifier;
    }

    /**
     * Get Target Replacement Identifier
     *
     * @return string
     */
    public function getTargetReplacementIdentifier()
    {
        return $this->targetReplacementIdentifier;
    }

    /**
     * Set a Target (ex: 'scripts/:controller/:action.:suffix')
     *
     * @param string
     * @return void
     */
    public function setTarget($target)
    {
        $this->target = (string) $target;
    }

    /**
     * Retrieve target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set target Reference
     *
     * @param  mixed &$target
     * @return void
     */
    public function setTargetReference(&$target)
    {
        $this->target = &$target;
    }

    /**
     * SetRules() is the same as calling addRules() with the exception that it
     * clears the rules before adding them.
     *
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules)
    {
        $this->clearRules();
        $this->addRules($rules);
    }

    /**
     * AddRules(): multi-call to setting filter rules.
     *
     * If prefixed with a ":" (colon), a filter rule will be added.  If not
     * prefixed, a static replacement will be added.
     *
     * ex:
     * array(
     *     ':controller' => array('CamelCaseToUnderscore', 'StringToLower'),
     *     ':action'     => array('CamelCaseToUnderscore', 'StringToLower'),
     *     'suffix'      => 'phtml'
     *     );
     *
     * @param array
     * @return void
     */
    public function addRules(array $rules)
    {
        $keys = array_keys($rules);

        foreach ($keys as $spec) {
            if ($spec[0] == ':') {
                $this->addFilterRule($spec, $rules[$spec]);
            } else {
                $this->setStaticRule($spec, $rules[$spec]);
            }
        }
    }

    /**
     * Get rules
     *
     * By default, returns all rules. If a $spec is provided, will return those
     * rules if found, false otherwise.
     *
     * @param  string $spec
     * @return array
     */
    public function getRules($spec = null)
    {
        if (null !== $spec) {
            $spec = $this->normalizeSpec($spec);

            if (isset($this->rules[$spec])) {
                return $this->rules[$spec];
            }

            return array();
        }

        return $this->rules;
    }

    /**
     * getRule() returns a rule set by setFilterRule(), a numeric index must be provided
     *
     * @param  string $spec
     * @param  int    $index
     * @return FilterInterface|null
     */
    public function getRule($spec, $index)
    {
        $spec = $this->normalizeSpec($spec);

        if (isset($this->rules[$spec]) && is_array($this->rules[$spec])) {
            if (isset($this->rules[$spec][$index])) {
                return $this->rules[$spec][$index];
            }
        }

        return null;
    }

    /**
     * Clears the rules currently in the inflector
     *
     * @return void
     */
    public function clearRules()
    {
        $this->rules = array();
    }

    /**
     * Set a filtering rule for a spec.  $ruleSet can be a string, Filter object
     * or an array of strings or filter objects.
     *
     * @param string $spec
     * @param array|string|\Zend\Filter\FilterInterface $ruleSet
     * @return void
     */
    public function setFilterRule($spec, $ruleSet)
    {
        $spec = $this->normalizeSpec($spec);

        $this->rules[$spec] = array();
        $this->addFilterRule($spec, $ruleSet);
    }

    /**
     * Add a filter rule for a spec
     *
     * @param mixed $spec
     * @param mixed $ruleSet
     * @return void
     */
    public function addFilterRule($spec, $ruleSet)
    {
        $spec = $this->normalizeSpec($spec);
        if (!isset($this->rules[$spec])) {
            $this->rules[$spec] = array();
        }

        if (!is_array($ruleSet)) {
            $ruleSet = array($ruleSet);
        }

        if (is_string($this->rules[$spec])) {
            $temp = $this->rules[$spec];
            $this->rules[$spec] = array();
            $this->rules[$spec][] = $temp;
        }

        foreach ($ruleSet as $rule) {
            $this->rules[$spec][] = $this->resolveRule($rule);
        }
    }

    /**
     * Set a static rule for a spec.  This is a single string value
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setStaticRule($name, $value)
    {
        $name = $this->normalizeSpec($name);
        $this->rules[$name] = (string) $value;
    }

    /**
     * Set Static Rule Reference.
     *
     * This allows a consuming class to pass a property or variable
     * in to be referenced when its time to build the output string from the
     * target.
     *
     * @param string $name
     * @param mixed $reference
     * @return void
     */
    public function setStaticRuleReference($name, &$reference)
    {
        $name = $this->normalizeSpec($name);
        $this->rules[$name] = &$reference;
    }

    /**
     * Inflect
     *
     * @param  string|array $source
     * @throws Exception\RuntimeException
     * @return string
     */
    public function filter($source)
    {
        // clean source
        foreach ((array) $source as $sourceName => $sourceValue) {
            $source[ltrim($sourceName, ':')] = $sourceValue;
        }

        $pregQuotedTargetReplacementIdentifier = preg_quote($this->targetReplacementIdentifier, '#');
        $processedParts = array();

        foreach ($this->rules as $ruleName => $ruleValue) {
            $processedPartKey = '#' . $pregQuotedTargetReplacementIdentifier . $ruleName . '#';

            if (isset($source[$ruleName])) {
                if (is_string($ruleValue)) {
                    // overriding the set rule
                    $processedParts[$processedPartKey] = str_replace('\\', '\\\\', $source[$ruleName]);
                } elseif (is_array($ruleValue)) {
                    $processedPart = $source[$ruleName];
                    foreach ($ruleValue as $ruleFilter) {
                        $processedPart = $ruleFilter($processedPart);
                    }
                    $processedParts[$processedPartKey] = str_replace('\\', '\\\\', $processedPart);
                }
            } elseif (is_string($ruleValue)) {
                $processedParts[$processedPartKey] = str_replace('\\', '\\\\', $ruleValue);
            }
        }

        // all of the values of processedParts would have been str_replace('\\', '\\\\', ..)'d to disable preg_replace backreferences
        $inflectedTarget = preg_replace(array_keys($processedParts), array_values($processedParts), $this->target);

        if ($this->throwTargetExceptionsOn && (preg_match('#(?=' . $pregQuotedTargetReplacementIdentifier.'[A-Za-z]{1})#', $inflectedTarget) == true)) {
            throw new Exception\RuntimeException(
                'A replacement identifier ' . $this->targetReplacementIdentifier . ' was found inside the inflected
                 target, perhaps a rule was not satisfied with a target source? Unsatisfied inflected target: ' . $inflectedTarget
            );
        }

        return $inflectedTarget;
    }

    /**
     * Normalize spec string
     *
     * @param  string $spec
     * @return string
     */
    private function normalizeSpec($spec)
    {
        return ltrim((string) $spec, ':&');
    }

    /**
     * Resolve named filters and convert them to filter objects.
     *
     * @param  FilterInterface|string $rule
     * @return FilterInterface
     */
    private function resolveRule($rule)
    {
        if ($rule instanceof FilterInterface) {
            return $rule;
        }

        return $this->filterPluginManager->get($rule);
    }
}
