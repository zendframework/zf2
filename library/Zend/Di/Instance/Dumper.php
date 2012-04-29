<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Di
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Di\Instance;

use Zend\Di\Di;

/**
 * Class for handling dumping of dependency injection parameters (recursively)
 *
 * @category  Zend
 * @package   Zend_Di
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @todo add FQCN to dumps (for aliases)
 */
class Dumper
{
    const REFERENCE = 0;
    const SCALAR    = 1;

    /**
     * @var Di
     */
    protected $di;

    /**
     * @var string
     */
    protected $instanceContext = array();

    /**
     * All the class dependencies [source][dependency]
     *
     * @var array
     */
    protected $currentDependencies = array();

    /**
     * @param Di $di
     */
    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    /**
     * @return array
     */
    public function getInitialInstanceDefinitions()
    {
        $im = $this->di->instanceManager();
        $classes = $im->getClasses();
        $aliases = array_keys($im->getAliases());
        return array_unique(array_merge($classes, $aliases));
    }

    /**
     * @return array
     */
    public function getAllInjectedDefinitions()
    {
        return $this->getInjectedDefinitions($this->getInitialInstanceDefinitions());
    }

    /**
     * @param string|array $name name or names of the instances to get
     *
     * @return array
     */
    public function getInjectedDefinitions($name)
    {
        $names = (array) $name;
        $visited = array();

        foreach ($names as $name) {
            $this->doGetInjectedDefinitions($name, $visited);
        }

        return $visited;
    }

    /**
     * @param string|array $name name or names of the instances to get
     * @param array $visited the array where discovered instance definitions will be stored
     *
     * @return mixed success of the operation
     */
    protected function doGetInjectedDefinitions($name, array &$visited)
    {
        $injectedDefinitions = array(
            'instantiator' => array(
                'name' => null,
                'parameters' => array(),
            ),
            'injections' => array(),
        );

        if (isset($visited[$name])) {
            return $visited[$name];
        }

        $definitions      = $this->di->definitions();
        $instanceManager = $this->di->instanceManager();

        if ($instanceManager->hasAlias($name)) {
            $class = $instanceManager->getClassFromAlias($name);
            $alias = $name;
        } else {
            $class = $name;
            $alias = null;
        }

        array_push($this->instanceContext, array('NEW', $class, $alias));

        if (!$definitions->hasClass($class)) {
            $aliasMsg = ($alias) ? '(specified by alias ' . $alias . ') ' : '';
            throw new \Zend\Di\Exception\ClassNotFoundException(
                'Class ' . $aliasMsg . $class . ' could not be located in provided definitions.'
            );
        }

        $instantiator     = $definitions->getInstantiator($class);
        $injectionMethods = $definitions->getMethods($class);

        $supertypeInjectionMethods = array();
        foreach ($definitions->getClassSupertypes($class) as $supertype) {
            $supertypeInjectionMethods[$supertype] = $definitions->getMethods($supertype);
        }

        if ('__construct' === $instantiator || null === $instantiator) {
            // $params forced to array() as we don't compile definitions for newInstanceWithParams
            $constructorParameters = $this->resolveMethodParameters(
                $class,
                '__construct',
                array(),
                true,
                $alias,
                true
            );

            if ($constructorParameters) {
                $injectedDefinitions['instantiator']['parameters'] = $constructorParameters;
            }

            if (array_key_exists('__construct', $injectionMethods)) {
                unset($injectionMethods['__construct']);
            }
        } elseif (is_callable($instantiator, false)) {
            if (!is_array($instantiator) && !is_string($instantiator)) {
                throw new Dumper\InvalidArgumentException(
                    'Unsupported: Callable instantiator must be a string or an array, "'
                    . gettype($instantiator) . '" provided'
                );
            }

            $injectedDefinitions['instantiator']['name'] = $instantiator;
            $callbackParams = $this->getCallbackParams(
                $instantiator,
                array(),
                $alias
            );

            if ($callbackParams) {
                $injectedDefinitions['instantiator']['parameters'] = $callbackParams;
            }
        } else {
            if (is_array($instantiator)) {
                $msg = sprintf(
                    'Invalid instantiator: %s::%s() is not callable.',
                    isset($instantiator[0]) ? $instantiator[0] : 'NoClassGiven',
                    isset($instantiator[1]) ? $instantiator[1] : 'NoMethodGiven'
                );
            } else {
                $msg = sprintf(
                    'Invalid instantiator of type "%s" for "%s".',
                    gettype($instantiator),
                    $name
                );
            }
            throw new Dumper\InvalidArgumentException($msg);
        }

        if ($injectionMethods || $supertypeInjectionMethods) {
            foreach ($injectionMethods as $injectionMethod => $methodIsRequired) {
                if ($injectionMethod !== '__construct') {
                    // $params forced to array() as we don't compile definitions for newInstanceWithParams
                    $injection = $this->resolveMethodParameters(
                        $class,
                        $injectionMethod,
                        array(),
                        false,
                        $alias,
                        $methodIsRequired
                    );
                    if ($injection) {
                        $injectedDefinitions['injections'][] = array(
                            'name' => $injectionMethod,
                            'parameters' => $injection,
                        );
                    }
                }
            }
            foreach ($supertypeInjectionMethods as $supertype => $supertypeInjectionMethod) {
                foreach ($supertypeInjectionMethod as $injectionMethod => $methodIsRequired) {

                    if ($injectionMethod !== '__construct') {
                        // $params forced to array() as we don't compile definitions for newInstanceWithParams
                        $injection = $this->resolveMethodParameters(
                            $supertype,
                            $injectionMethod,
                            array(),
                            false,
                            $alias,
                            $methodIsRequired
                        );
                        if ($injection) {
                            $injectedDefinitions['injections'][] = array(
                                'name' => $injectionMethod,
                                'parameters' => $injection,
                            );
                        }
                    }
                }
            }

            $instanceConfiguration = $instanceManager->getConfiguration($name);

            if ($instanceConfiguration['injections']) {
                $objectsToInject = $methodsToCall = array();
                foreach ($instanceConfiguration['injections'] as $injectName => $injectValue) {
                    if (is_int($injectName) && is_string($injectValue)) {
                        $objectsToInject[] = $injectValue;
                        // $objectsToInject[] = $this->get($injectValue, $params);
                    } elseif (is_string($injectName) && is_array($injectValue)) {
                        if (is_string(key($injectValue))) {
                            $methodsToCall[] = array('method' => $injectName, 'args' => $injectValue);
                        } else {
                            foreach ($injectValue as $methodCallArgs) {
                                $methodsToCall[] = array('method' => $injectName, 'args' => $methodCallArgs);
                            }
                        }
                    } elseif (is_object($injectValue)) {
                        throw new Dumper\InvalidArgumentException(
                            'Unsupported: directly injecting object instances is not supported, instance of "'
                            . get_class($injectValue) . '" provided'
                        );
                    } elseif (is_int($injectName) && is_array($injectValue)) {
                        throw new \Zend\Di\Exception\RuntimeException(
                            'An injection was provided with a keyed index and an array of data, try using'
                            . ' the name of a particular method as a key for your injection data.'
                        );
                    }
                }

                if ($objectsToInject) {
                    foreach ($objectsToInject as $objectToInject) {
                        foreach ($injectionMethods as $injectionMethod => $methodIsRequired) {
                            $methodParams = $definitions->getMethodParameters($class, $injectionMethod);
                            if ($methodParams) {
                                foreach ($methodParams as $methodParam) {
                                    $objectToInjectClass = $instanceManager->hasAlias($objectToInject)
                                        ? $instanceManager->getClassFromAlias($objectToInject)
                                        : $objectToInject;
                                    if (
                                        class_exists($objectToInjectClass)
                                        && (
                                            $objectToInjectClass === $methodParam[1]
                                            || $this->isSubclassOf($objectToInjectClass, $methodParam[1])
                                        )
                                    ) {
                                        $callParams = $this->resolveMethodParameters(
                                            $class,
                                            $injectionMethod,
                                            array($methodParam[0] => $objectToInject),
                                            false,
                                            $alias,
                                            true
                                        );
                                        if ($callParams) {
                                            $injectedDefinitions['injections'][] = array(
                                                'name' => $injectionMethod,
                                                'parameters' => $callParams,
                                            );
                                        }
                                        continue 3;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($methodsToCall) {
                    foreach ($methodsToCall as $methodInfo) {
                        $callParams = $this->resolveMethodParameters(
                            $class,
                            $methodInfo['method'],
                            $methodInfo['args'],
                            false,
                            $alias,
                            true
                        );

                        if ($callParams) {
                            $injectedDefinitions['injections'][] = array(
                                'name' => $methodInfo['method'],
                                'parameters' => $callParams,
                            );
                        }
                    }
                }
            }
        }

        array_pop($this->instanceContext);

        $visited[$name] = $injectedDefinitions;

        // recursively discover dependencies and convert generatedReference and Scalar parameters to array
        foreach ($injectedDefinitions['instantiator']['parameters'] as $key => $parameter) {
            if ($parameter instanceof Dumper\ReferenceParameter) {
                /* @var $parameter Dumper\ReferenceParameter */
                $this->doGetInjectedDefinitions($parameter->getReferenceId(), $visited);
            }
            $visited[$name]['instantiator']['parameters'][$key] = $parameter->toArray();
        }
        foreach ($injectedDefinitions['injections'] as $method => $methodCall) {
            foreach ($methodCall['parameters'] as $key => $parameter) {
                if ($parameter instanceof Dumper\ReferenceParameter) {
                    /* @var $parameter Dumper\ReferenceParameter */
                    $this->doGetInjectedDefinitions($parameter->getReferenceId(), $visited);
                }
                $visited[$name]['injections'][$method]['parameters'][$key] = $parameter->toArray();
            }
        }

        return $visited;
    }

    /**
     * Get parameters for the defined callback
     *
     * @param callback $callback
     * @param array $params
     * @param string $alias
     * @return object
     * @throws Dumper\InvalidArgumentException
     */
    protected function getCallbackParams($callback, $params, $alias)
    {
        if (!is_callable($callback)) {
            throw new Dumper\InvalidArgumentException('An invalid constructor callback was provided');
        }

        if (is_array($callback)) {
            $class = (is_object($callback[0])) ? get_class($callback[0]) : $callback[0];
            $method = $callback[1];
        } elseif (is_string($callback) && strpos($callback, '::') !== false) {
            list($class, $method) = explode('::', $callback, 2);
        } else {
            throw new Dumper\InvalidArgumentException('Invalid callback type provided to ' . __METHOD__);
        }

        if ($this->di->definitions()->hasMethod($class, $method)) {
            return $this->resolveMethodParameters($class, $method, $params, true, $alias, true);
        }
        return array();
    }

    /**
     * Resolve parameters referencing other services
     *
     * @param string $class
     * @param string $method
     * @param array $callTimeUserParams
     * @param bool $isInstantiator
     * @param string $alias
     * @param bool $methodIsRequired
     * @return array an array of ordered parameters
     */
    protected function resolveMethodParameters($class, $method, array $callTimeUserParams, $isInstantiator, $alias, $methodIsRequired)
    {
        $im = $this->di->instanceManager();
        // parameters for this method, in proper order, to be returned
        $resolvedParams = array();

        // parameter requirements from the definition
        $injectionMethodParameters = $this->di->definitions()->getMethodParameters($class, $method);

        // computed parameters array
        $computedParams = array(
            'value'    => array(),
            'required' => array(),
            'optional' => array()
        );

        // retrieve instance configurations for all contexts
        $iConfig = array();
        $aliases = $im->getAliases();

        // for the alias in the dependency tree
        if ($alias && $im->hasConfiguration($alias)) {
            $iConfig['thisAlias'] = $im->getConfiguration($alias);
        }

        // for the current class in the dependency tree
        if ($im->hasConfiguration($class)) {
            $iConfig['thisClass'] = $im->getConfiguration($class);
        }

        // for the parent class, provided we are deeper than one node
        list($requestedClass, $requestedAlias) = ($this->instanceContext[0][0] == 'NEW')
            ? array($this->instanceContext[0][1], $this->instanceContext[0][2])
            : array($this->instanceContext[1][1], $this->instanceContext[1][2]);

        if ($requestedClass != $class && $im->hasConfiguration($requestedClass)) {
            $iConfig['requestedClass'] = $im->getConfiguration($requestedClass);
            if ($requestedAlias) {
                $iConfig['requestedAlias'] = $im->getConfiguration($requestedAlias);
            }
        }

        // This is a 2 pass system for resolving parameters
        // first pass will find the sources, the second pass will order them and resolve lookups if they exist
        // MOST methods will only have a single parameters to resolve, so this should be fast

        foreach ($injectionMethodParameters as $fqParamPos => $info) {
            list($name, $type, $isRequired) = $info;

            $fqParamName = substr_replace($fqParamPos, ':' . $info[0], strrpos($fqParamPos, ':'));

            // PRIORITY 1 - consult user provided parameters
            if (isset($callTimeUserParams[$fqParamPos]) || isset($callTimeUserParams[$name])) {

                if (isset($callTimeUserParams[$fqParamPos])) {
                    $callTimeCurValue =& $callTimeUserParams[$fqParamPos];
                } elseif (isset($callTimeUserParams[$fqParamName])) {
                    $callTimeCurValue =& $callTimeUserParams[$fqParamName];
                } else {
                    $callTimeCurValue =& $callTimeUserParams[$name];
                }

                if (is_string($callTimeCurValue)) {
                    if ($im->hasAlias($callTimeCurValue)) {
                        // was an alias provided?
                        $computedParams['required'][$fqParamPos] = array(
                            // @todo check if scalar of reference here?
                            new Dumper\ReferenceParameter($callTimeUserParams[$name]),
                            $im->getClassFromAlias($callTimeCurValue)
                        );
                    } elseif ($this->di->definitions()->hasClass($callTimeUserParams[$name])) {
                        // was a known class provided?
                        $computedParams['required'][$fqParamPos] = array(
                            new Dumper\ReferenceParameter($callTimeCurValue),
                            $callTimeCurValue
                        );
                    } else {
                        // must be a value
                        $computedParams['value'][$fqParamPos] = new Dumper\ScalarParameter($callTimeCurValue);
                    }
                } else {
                    // int, float, null, object, etc
                    // @todo scalar param
                    $computedParams['value'][$fqParamPos] = new Dumper\ScalarParameter($callTimeCurValue);
                }
                unset($callTimeCurValue);
                continue;
            }

            // PRIORITY 2 -specific instance configuration (thisAlias) - this alias
            // PRIORITY 3 -THEN specific instance configuration (thisClass) - this class
            // PRIORITY 4 -THEN specific instance configuration (requestedAlias) - requested alias
            // PRIORITY 5 -THEN specific instance configuration (requestedClass) - requested class

            foreach (array('thisAlias', 'thisClass', 'requestedAlias', 'requestedClass') as $thisIndex) {
                // check the provided parameters config
                if (isset($iConfig[$thisIndex]['parameters'][$fqParamPos])
                    || isset($iConfig[$thisIndex]['parameters'][$fqParamName])
                    || isset($iConfig[$thisIndex]['parameters'][$name])) {

                    if (isset($iConfig[$thisIndex]['parameters'][$fqParamPos])) {
                        $iConfigCurValue =& $iConfig[$thisIndex]['parameters'][$fqParamPos];
                    } elseif (isset($iConfig[$thisIndex]['parameters'][$fqParamName])) {
                        $iConfigCurValue =& $iConfig[$thisIndex]['parameters'][$fqParamName];
                    } else {
                        $iConfigCurValue =& $iConfig[$thisIndex]['parameters'][$name];
                    }

                    if (
                        is_string($iConfigCurValue)
                        && $type === false
                    ) {
                        $computedParams['value'][$fqParamPos] = new Dumper\ScalarParameter($iConfigCurValue);
                    } elseif (
                        is_string($iConfigCurValue)
                        && isset($aliases[$iConfigCurValue])
                    ) {
                        $computedParams['required'][$fqParamPos] = array(
                            new Dumper\ReferenceParameter($iConfig[$thisIndex]['parameters'][$name]),
                            $im->getClassFromAlias($iConfigCurValue)
                        );
                    } elseif (
                        is_string($iConfigCurValue)
                        && $this->di->definitions()->hasClass($iConfigCurValue)
                    ) {
                        $computedParams['required'][$fqParamPos] = array(
                            new Dumper\ReferenceParameter($iConfigCurValue),
                            $iConfigCurValue
                        );
                    } elseif (
                        is_object($iConfigCurValue)
                        && $iConfigCurValue instanceof \Closure
                        && $type !== 'Closure'
                    ) {
                        throw new Dumper\InvalidArgumentException(
                            'Unsupported: cannot use instances as method parameters, instance of "'
                            . get_class($iConfigCurValue) . '" provided'
                        );
                    } else {
                        if( is_object($iConfigCurValue)) {
                            throw new Dumper\InvalidArgumentException(
                                'Unsupported: cannot use instances as method parameters, instance of "'
                                . get_class($iConfigCurValue) . '" provided'
                            );
                        }
                        $computedParams['value'][$fqParamPos] = new Dumper\ScalarParameter($iConfigCurValue);
                    }
                    unset($iConfigCurValue);
                    continue 2;
                }

            }

            // PRIORITY 6 - globally preferred implementations

            // next consult alias level preferred instances
            if ($alias && $im->hasTypePreferences($alias)) {
                $pInstances = $im->getTypePreferences($alias);
                foreach ($pInstances as $pInstance) {

                    /*if (is_object($pInstance)) {
                        // @todo scalar param - injection should be a reference!
                        throw new \BadMethodCallException('Not implemented yet');
                        // $computedParams['value'][$fqParamPos] = new Dumper\ReferenceParameter($pInstance);
                        continue 2;
                    }*/

                    $pInstance = is_object($pInstance) ? get_class($pInstance) : $pInstance;
                    $pInstanceClass = ($im->hasAlias($pInstance)) ? $im->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || $this->isSubclassOf($pInstanceClass, $type)) {
                        $computedParams['required'][$fqParamPos] = array(
                            new Dumper\ReferenceParameter($pInstance),
                            $pInstanceClass
                        );
                        continue 2;
                    }
                }
            }

            // next consult class level preferred instances
            if ($type && $im->hasTypePreferences($type)) {
                $pInstances = $im->getTypePreferences($type);
                foreach ($pInstances as $pInstance) {

                    /*if (is_object($pInstance)) {
                        // @todo scalar param - injection should be a reference!
                        throw new \BadMethodCallException('Not implemented yet');
                        //$computedParams['value'][$fqParamPos] = new Dumper\ScalarParameter($pInstance);
                        continue 2;
                    }*/

                    $pInstance = is_object($pInstance) ? get_class($pInstance) : $pInstance;
                    $pInstanceClass = ($im->hasAlias($pInstance)) ?  $im->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || $this->isSubclassOf($pInstanceClass, $type)) {
                        $computedParams['required'][$fqParamPos] = array(
                            new Dumper\ReferenceParameter($pInstance),
                            $pInstanceClass
                        );
                        continue 2;
                    }
                }
            }

            if (!$isRequired) {
                $computedParams['optional'][$fqParamPos] = true;
            }

            if ($type && $isRequired && $methodIsRequired) {
                $computedParams['required'][$fqParamPos] = array(
                    new Dumper\ReferenceParameter($type),
                    $type
                );
            }

        }

        $index = 0;
        foreach ($injectionMethodParameters as $fqParamPos => $value) {
            $name = $value[0];

            if (isset($computedParams['value'][$fqParamPos])) {

                // if there is a value supplied, use it
                //$resolvedParams[$index] = $computedParams['value'][$fqParamPos];
                $resolvedParams[$index] = $computedParams['value'][$fqParamPos];

            } elseif (isset($computedParams['required'][$fqParamPos])) {

                // detect circular dependencies! (they can only happen in instantiators)
                if ($isInstantiator && in_array($computedParams['required'][$fqParamPos][1], $this->currentDependencies)) {
                    throw new \Zend\Di\Exception\CircularDependencyException(
                        "Circular dependency detected: $class depends on {$value[1]} and viceversa"
                    );
                }
                array_push($this->currentDependencies, $class);
                $resolvedParams[$index] = $computedParams['required'][$fqParamPos][0];

                /* @todo do following distinction while compiling?
                $dConfig = $im->getConfiguration($computedParams['required'][$fqParamPos][0]);
                if ($dConfig['shared'] === false) {
                    //$resolvedParams[$index] = $computedParams['required'][$fqParamPos][0];
                    $resolvedParams[$index] = new Dumper\ReferenceParameter($computedParams['required'][$fqParamPos][0]);
                    //$resolvedParams[$index] = $this->newInstance($computedParams['required'][$fqParamPos][0], $callTimeUserParams, false);
                } else {
                    //$resolvedParams[$index] = $computedParams['required'][$fqParamPos][0];
                    $resolvedParams[$index] = new Dumper\ReferenceParameter($computedParams['required'][$fqParamPos][0]);
                    //$resolvedParams[$index] = $this->get($computedParams['required'][$fqParamPos][0], $callTimeUserParams);
                }*/

                array_pop($this->currentDependencies);

            } elseif (!array_key_exists($fqParamPos, $computedParams['optional'])) {

                if ($methodIsRequired) {
                    // if this item was not marked as optional,
                    // plus it cannot be resolve, and no value exist, bail out
                    throw new \Zend\Di\Exception\MissingPropertyException(sprintf(
                        'Missing %s for parameter ' . $name . ' for ' . $class . '::' . $method,
                        (($value[0] === null) ? 'value' : 'instance/object' )
                    ));
                } else {
                    return false;
                }

            } else {
                $resolvedParams[$index] = new Dumper\ScalarParameter(null);
            }

            $index++;
        }

        return $resolvedParams;
    }

    /**
     * @see https://bugs.php.net/bug.php?id=53727
     *
     * @param $class
     * @param $type
     * @return bool
     */
    protected function isSubclassOf($class, $type)
    {
        /* @var $isSubclassFunc Closure */
        static $isSubclassFuncCache = null; // null as unset, array when set

        if ($isSubclassFuncCache === null) {
            $isSubclassFuncCache = array();
        }

        if (!array_key_exists($class, $isSubclassFuncCache)) {
            $isSubclassFuncCache[$class] = class_parents($class, true) + class_implements($class, true);
        }
        return (isset($isSubclassFuncCache[$class][$type]));
    }
}
