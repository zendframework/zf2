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
use Zend\Di\Configuration;


/**
 * Class for handling dumping of dependency injection parameters (recursively)
 *
 * @category  Zend
 * @package   Zend_Di
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @todo doesn't handle multiple injections/subsequent method calls for now! Check
 * all call_user_func issued by Zend\Di\Di!
 */
class Dumper
{
    /**
     * @var Di
     */
    protected $di;

    // @todo eventually remove to reduce code duplication
    protected $instanceContext = array();

    // @todo eventually remove to reduce code duplication
    protected $currentDependencies = array();

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function getInitialInstanceDefinitions()
    {
        $im = $this->di->instanceManager();
        $classes = $im->getClasses();
        $aliases = array_keys($im->getAliases());
        return array_unique(array_merge($classes, $aliases));
    }

    /**
     * @return Di
     * @todo maybe not needed
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * @return array
     */
    public function getAllInjectedDefinitions()
    {
        return $this->getInjectedDefinitions($this->getInitialInstanceDefinitions());
    }

    // @todo lots of code duplication in here!
    // @todo return a list of injected definitions in form
    // array(
    //     'methodName' => array(
    //         'param_1' => false, // false if it is not another dependency
    //         'param_2' => array(
    //              // *recursion*
    //          ),
    //     ),  // true if it is a definition and requires recursive checking
    //     'otherMethod' => ...
    // )

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
        $injectedDefinitions = array();

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
            // @todo return empty array instead (can't compile?) ?
            return $visited[$name] = false;
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

        // @todo checking NULL instantiator (bug? not sure for now)
        // @todo what if no constructor? :P
        if ('__construct' === $instantiator || null === $instantiator) {
            // $params forced to array() as we don't compile definitions for newInstanceWithParams
            $injectedDefinitions['__construct'] = $this->getConstructorParams($class, array(), $alias);

            //$instance = $this->createInstanceViaConstructor($class, $params, $alias);
            if (array_key_exists('__construct', $injectionMethods)) {
                unset($injectionMethods['__construct']);
            }
        } elseif (is_callable($instantiator, false)) {
            if(!is_array($instantiator) && !is_string($instantiator)) {
                throw new \BadMethodCallException('unsupported?');
            }
            // @todo $instance = $this->createInstanceViaCallback($instantiator, $params, $alias);
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
            throw new \RuntimeException($msg);
        }

        if ($injectionMethods || $supertypeInjectionMethods) {
            foreach ($injectionMethods as $injectionMethod => $methodIsRequired) {
                if ($injectionMethod !== '__construct') {
                    // $params forced to array() as we don't compile definitions for newInstanceWithParams
                    $injection = $this->getInjectionMethodsForInstance($injectionMethod, array(), $alias, $methodIsRequired, $class);
                    // @todo what if the $injection is actually something that casts to false?
                    if($injection) {
                        $injectedDefinitions[$injectionMethod] = $injection;
                    }
                }
            }
            foreach ($supertypeInjectionMethods as $supertype => $supertypeInjectionMethod) {
                foreach ($supertypeInjectionMethod as $injectionMethod => $methodIsRequired) {
                    if ($injectionMethod !== '__construct') {
                        $injection = $this->getInjectionMethodsForInstance($injectionMethod, array(), $alias, $methodIsRequired, $supertype);
                        // @todo what if the $injection is actually something that casts to false?
                        if($injection) {
                            $injectedDefinitions[$injectionMethod] = $injection;
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
                        throw new \BadMethodCallException('unsupported?');
                        // $objectsToInject[] = $injectValue;
                    } elseif (is_int($injectName) && is_array($injectValue)) {
                        // @todo must find method name somehow
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
                                    $objectsToInject = is_object($objectsToInject) ? get_class($objectsToInject) : $objectToInject;
                                    if (
                                        $objectToInject === $methodParam[1]
                                        || $this->isSubclassOf($objectToInject, $methodParam[1])
                                    ) {
                                        // @todo restore $params used inside resolveMethodParameters?
                                        $callParams = $this->resolveMethodParameters($class, $injectionMethod,
                                            array($methodParam[0] => $objectToInject), false, $alias, true
                                        );
                                        /**
                                        $callParams = $this->resolveMethodParameters(get_class($instance), $injectionMethod,
                                            array($methodParam[0] => $objectToInject), false, $alias, true
                                        );
                                         * */
                                        if ($callParams) {
                                            $injectedDefinitions[$injectionMethod] = $callParams;
                                            //var_dump($injectionMethod, $callParams);
                                        }
                                        /*if ($callParams) {
                                            call_user_func_array(array($instance, $injectionMethod), $callParams);
                                        }*/
                                        continue 3;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($methodsToCall) {
                    foreach ($methodsToCall as $methodInfo) {
                        $callParams = $this->resolveMethodParameters(get_class($instance), $methodInfo['method'],
                            $methodInfo['args'], false, $alias, true
                        );
                        call_user_func_array(array($instance, $methodInfo['method']), $callParams);
                    }
                }
            }
        }

        array_pop($this->instanceContext);

        $visited[$name] = $injectedDefinitions;

        foreach ($visited[$name] as $method => $injections) {
            if (is_array($injections)) {
                foreach ($injections as $injectionName) {
                    // @todo check scalar $injectionName here!
                    if (is_string($injectionName) && !isset($visited[$injectionName])) {
                        $this->doGetInjectedDefinitions($injectionName, $visited);
                    }
                }
            }
        }

        //return $visited[$name];
        return $visited;
    }

    protected function getConstructorParams($class, array $params, $alias)
    {
        if ($this->di->definitions()->hasMethod($class, '__construct')) {
            return $this->resolveMethodParameters($class, '__construct', $params, true, $alias, true);
        }
        return null;
    }

    /**
     * This parameter will handle any injection methods and resolution of
     * dependencies for such methods
     *
     * @param object $object
     * @param string $method
     * @param array $params
     * @param string $alias
     */
    protected function getInjectionMethodsForInstance($method, $params, $alias, $methodIsRequired, $methodClass)
    {
        // @todo make sure to resolve the supertypes for both the object & definition
        return $this->resolveMethodParameters($methodClass, $method, $params, false, $alias, $methodIsRequired);
        /*$callParameters = $this->resolveMethodParameters($methodClass, $method, $params, false, $alias, $methodIsRequired);
        if ($callParameters == false) {
            return;
        } */
    }


    /**
     * Resolve parameters referencing other services
     *
     * @param string $class
     * @param string $method
     * @param array $callTimeUserParams
     * @param bool $isInstantiator
     * @param string $alias
     * @return array
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
                            $callTimeUserParams[$name],
                            $im->getClassFromAlias($callTimeCurValue)
                        );
                    } elseif ($this->di->definitions()->hasClass($callTimeUserParams[$name])) {
                        // was a known class provided?
                        $computedParams['required'][$fqParamPos] = array(
                            $callTimeCurValue,
                            $callTimeCurValue
                        );
                    } else {
                        // must be a value
                        $computedParams['value'][$fqParamPos] = $callTimeCurValue;
                    }
                } else {
                    // int, float, null, object, etc
                    $computedParams['value'][$fqParamPos] = $callTimeCurValue;
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

                    if (is_string($iConfigCurValue)
                        && $type === false) {
                        $computedParams['value'][$fqParamPos] = $iConfigCurValue;
                    } elseif (is_string($iConfigCurValue)
                        && isset($aliases[$iConfigCurValue])) {
                        $computedParams['required'][$fqParamPos] = array(
                            $iConfig[$thisIndex]['parameters'][$name],
                            $im->getClassFromAlias($iConfigCurValue)
                        );
                    } elseif (is_string($iConfigCurValue)
                        && $this->di->definitions()->hasClass($iConfigCurValue)) {
                        $computedParams['required'][$fqParamPos] = array(
                            $iConfigCurValue,
                            $iConfigCurValue
                        );
                    } elseif (is_object($iConfigCurValue)
                        && $iConfigCurValue instanceof \Closure
                        && $type !== 'Closure') {
                        // @todo can't handle?
                        throw new \BadMethodCallException('unsupported?');
                        $computedParams['value'][$fqParamPos] = $iConfigCurValue();
                    } else {
                        // @todo can't handle?
                        if( is_object($iConfigCurValue)) {
                            throw new \BadMethodCallException('unsupported?');
                        }
                        $computedParams['value'][$fqParamPos] = $iConfigCurValue;
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
                    if (is_object($pInstance)) {
                        $computedParams['value'][$fqParamPos] = $pInstance;
                        continue 2;
                    }
                    $pInstanceClass = ($im->hasAlias($pInstance)) ?
                        $im->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || $this->isSubclassOf($pInstanceClass, $type)) {
                        $computedParams['required'][$fqParamPos] = array($pInstance, $pInstanceClass);
                        continue 2;
                    }
                }
            }

            // next consult class level preferred instances
            if ($type && $im->hasTypePreferences($type)) {
                $pInstances = $im->getTypePreferences($type);
                foreach ($pInstances as $pInstance) {
                    /*
                    if (is_object($pInstance)) {
                        // @todo can't handle?
                        $computedParams['value'][$fqParamPos] = $pInstance;
                        continue 2;
                    }
                    */
                    // @todo enforcing alias from object if possible
                    $pInstance = is_object($pInstance) ? get_class($pInstance) : $pInstance;
                    $pInstanceClass = ($im->hasAlias($pInstance)) ?
                        $im->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || $this->isSubclassOf($pInstanceClass, $type)) {
                        $computedParams['required'][$fqParamPos] = array($pInstance, $pInstanceClass);
                        continue 2;
                    }
                }
            }

            if (!$isRequired) {
                $computedParams['optional'][$fqParamPos] = true;
            }

            if ($type && $isRequired && $methodIsRequired) {
                $computedParams['required'][$fqParamPos] = array($type, $type);
            }

        }

        $index = 0;
        foreach ($injectionMethodParameters as $fqParamPos => $value) {
            $name = $value[0];

            if (isset($computedParams['value'][$fqParamPos])) {

                // if there is a value supplied, use it
                $resolvedParams[$index] = $computedParams['value'][$fqParamPos];

            } elseif (isset($computedParams['required'][$fqParamPos])) {

                // detect circular dependencies! (they can only happen in instantiators)
                if ($isInstantiator && in_array($computedParams['required'][$fqParamPos][1], $this->currentDependencies)) {
                    throw new \Zend\Di\Exception\CircularDependencyException(
                        "Circular dependency detected: $class depends on {$value[1]} and viceversa"
                    );
                }
                array_push($this->currentDependencies, $class);
                $dConfig = $im->getConfiguration($computedParams['required'][$fqParamPos][0]);
                // @todo do that distinction in compiling stuff?
                if ($dConfig['shared'] === false) {
                    $resolvedParams[$index] = $computedParams['required'][$fqParamPos][0];
                    //$resolvedParams[$index] = $this->newInstance($computedParams['required'][$fqParamPos][0], $callTimeUserParams, false);
                } else {
                    $resolvedParams[$index] = $computedParams['required'][$fqParamPos][0];
                    //$resolvedParams[$index] = $this->get($computedParams['required'][$fqParamPos][0], $callTimeUserParams);
                }

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
                $resolvedParams[$index] = null;
            }

            $index++;
        }

        return $resolvedParams; // return ordered list of parameters
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
