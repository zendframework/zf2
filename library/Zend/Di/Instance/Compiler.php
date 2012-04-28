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

use Zend\Code\Generator as CodeGen;

/**
 * Class for generating service locators from Di instances
 *
 * @category  Zend
 * @package   Zend_Di
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Compiler
{
    protected $containerClass = 'ApplicationContext';

    protected $definitions;

    protected $instances = array();

    protected $namespace;

    protected $aliases;

    /**
     * Constructor
     *
     * @param  array $data
     * @return void
     */
    public function __construct(Dumper $dumper)
    {
        $this->instances = $dumper->getAllInjectedDefinitions();
        $this->aliases = $dumper->getAliases();
        $this->definitions = $dumper->getDefinitions();
    }

    /**
     * Set the class name for the generated service locator container
     *
     * @param  string $name
     * @return Compiler
     */
    public function setContainerClass($name)
    {
        $this->containerClass = $name;
        return $this;
    }

    /**
     * Set the namespace to use for the generated class file
     *
     * @param  string $namespace
     * @return Compiler
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Construct, configure, and return a PHP classfile code generation object
     *
     * Creates a Zend\CodeGenerator\Php\PhpFile object that has
     * created the specified class and service locator methods.
     *
     * @param  null|string $filename
     * @return CodeGen\PhpFile
     */
    public function getCodeGenerator($filename = null)
    {
        $indent         = '    ';
        $caseStatements = array();
        $getters        = array();
        $instances    = $this->instances;

        foreach ($instances as $name => $data) {
            if (!$data) {
                continue;
            }
            $getter = $this->normalizeAlias($name);

            $instantiationParams = isset($data['__construct']) ? $data['__construct'] : array();
            unset($data['__construct']);

            $methods = $data;

            $injectionParams = array();
            foreach ($methods as $method => $params) {
                $injectionParams[$method] = $params;
            }
            //$params = array();
            // Build parameter list for instantiation
            foreach ($instantiationParams as $key => $param) {
                /*if(is_string($param)) {
                    $params[$key] = sprintf('$this->%s()', $this->normalizeAlias($param['type']));
                }*/
                // @todo other param types (scalars, closures, references?)
                // @todo all existing types are defined here, all the rest should probably be scalar
                // @todo (check if $definitions[$name] !== false)
                /**
                if (null === $param['type'] ||
                    in_array($param['type'], array('string', 'array', 'integer', 'boolean'))
                ) {
                    $type = $param['type'];
                    $param = isset($instances[$name]['parameters'][$param['name']]) ?
                        $instances[$name]['parameters'][$param['name']] : NULL;
                    switch (strtolower($type)) {
                        case 'integer':
                            $param = (int) $param;
                            break;
                        case 'boolean':
                            $param = (bool) $param;
                            break;
                    }
                    $string = preg_replace( '/\\\{2,}/', '\\', var_export($param, 1));

                    if (strstr($string, '::__set_state(')) {
                        throw new \RuntimeException(
                            'Arguments in definitions may not contain objects'
                        );
                    }
                    $params[$key] = $string;
                } elseif (isset($param['type']) && $param['type'] == '$this') {
                    $params[$key] = '$this';
                } elseif (isset($param['type'])) {
                    $params[$key] = sprintf('$this->%s()', $this->normalizeAlias($param['type']));
                } else {
                    $message = sprintf(
                        'Unable to use object arguments when building containers. Encountered with "%s", '
                            . 'parameter of type "%s"', $name, get_class($param));
                    throw new \RuntimeException($message);
                }
                 */
            }

            // Create instantiation code
            //$constructor = $instantiator;
            // @todo callbacks?
            $constructor = '__construct';
            if ('__construct' != $constructor) {
                // @todo check this part
                throw new \RuntimeException('unsupported');
                // Constructor callback
                /*if (is_callable($constructor)) {
                    $callback = $constructor;
                    if (is_array($callback)) {
                        $class = (is_object($callback[0])) ? get_class($callback[0]) : $callback[0];
                        $method = $callback[1];
                    } elseif (is_string($callback) && strpos($callback, '::') !== false) {
                        list($class, $method) = explode('::', $callback, 2);
                    }
                    $callback = var_export(array($class, $method), 1);
                    if (count($params)) {
                        $creation = sprintf('$object = call_user_func(%s, %s);', $callback, implode(', ', $params));
                    } else {
                        $creation = sprintf('$object = call_user_func(%s);', $callback);
                    }
                } else if (is_string($constructor) && strpos($constructor, '->') !== false) {
                    list($class, $method) = explode('->', $constructor, 2);
                    if (!class_exists($class)) {
                        throw new \InvalidArgumentException('No class found: ' . $class);
                    }
                    $factoryGetter = $this->normalizeAlias($class);
                    if (count($params)) {
                        $creation = sprintf('$object = $this->' . $factoryGetter . '()->%s(%s);', $method, implode(', ', $params));
                    } else {
                        $creation = sprintf('$object = $this->' . $factoryGetter . '()->%s();', $method);
                    }
                } else {
                    throw new \InvalidArgumentException('Invalid constructor supplied for class: ' . $name);
                }*/
            } else {
                // Normal instantiation
                $className = $this->reduceAlias($name);
                $className = '\\' . ltrim($className, '\\');
                $instantiator = $this->definitions->getInstantiator($name);

                $param = '';
                $it = new \CachingIterator(new \ArrayIterator($params));
                foreach ($it as $value) {
                    if (is_string($value)) {
                        $param .= sprintf('$this->%s()', $this->normalizeAlias($value));
                    } else {
                        $param .= var_export($value, 1);
                    }
                    if ($it->hasNext()) {
                        $param .= ', ';
                    }
                }
                if ($this->instances && $instantiator == '__construct') {
                    $creation = sprintf('$object = new %s(%s);', $className, $param);
                } else if ($instances && is_array($instantiator) && is_callable($instantiator)) {
                    $creation = sprintf('$object = ' . $instantiator[0] . '::' . $instantiator[1] . '(%s);', $param);
                }
            }
                // Normal instantiation
                //$className = '\\' . ltrim($this->reduceAlias($name), '\\');
                //$creation = sprintf('$object = new %s(%s);', $className, implode(', ', $params));
            //}

            $params = array();
            // Create method call code
            $methods = '';
            foreach ($injectionParams as $key => $methodData) {
                $methodName   = $key;
                $methodParams = $methodData;

                // Create method parameter representation
                foreach ($methodParams as $key => $param) {
                    if(is_string($param)) {
                        $params[$key] = sprintf('$this->%s()', $this->normalizeAlias($param));
                    } else {
                        $params[$key] .= var_export($value, 1);
                    }
                    //$creation = sprintf('$object = new %s(%s);', $className, $param);
                    /**
                    if (!isset($param['type'])) $param['type'] = NULL;
                    if (!isset($param['name'])) $param['name'] = NULL;
                    if (null === $param['type'] ||
                        in_array($param['type'], array('string', 'array', 'integer', 'boolean'))
                    ) {
                        $type = $param['type'];
                        $param = isset($instances[$name]['parameters'][$param['name']]) ?
                            $instances[$name]['parameters'][$param['name']] : NULL;
                        switch (strtolower($type)) {
                            case 'integer':
                                $param = (int) $param;
                                break;
                            case 'boolean':
                                $param = (bool) $param;
                                break;
                        }

                        $string = preg_replace('/\\\{2,}/', '\\', var_export($param, 1));
                        if (strstr($string, '::__set_state(')) {
                            throw new \RuntimeException(
                                'Arguments in definitions may not contain objects'
                            );
                        }
                        $params[$key] = $string;
                    } elseif (isset($param['type']) && $param['type'] == '$this') {
                        $params[$key] = '$this';
                    } elseif (isset($param['type'])) {
                        $params[$key] = sprintf(
                            '$this->%s()', $this->normalizeAlias($param['type'])
                        );
                    } else {
                        $message = sprintf(
                            'Unable to use object arguments when building containers. '
                                . 'Encountered with "%s", parameter of type "%s"', $name,
                            get_class($param));
                        throw new \RuntimeException($message);
                    }*/
                }

                $methods .= sprintf("\$object->%s(%s);\n", $methodName, implode(', ', $params));
                $params = array();
            }

            $storage = '';

            // Start creating getter
            $getterBody = '';

            // Creation and method calls
            $getterBody .= sprintf("%s\n", $creation);
            $getterBody .= $methods;

            // Stored service
            $getterBody .= $storage;

            // End getter body
            $getterBody .= "return \$object;\n";

            $getterDef = new CodeGen\MethodGenerator();
            $getterDef->setName($getter)
                ->setBody($getterBody);
            $getters[] = $getterDef;

            // Build case statement and store
            $statement = '';
            $statement .= sprintf("%scase '%s':\n", $indent, $name);
            $statement .= sprintf("%sreturn \$this->%s();\n", str_repeat($indent, 2), $getter);

            $caseStatements[] = $statement;
        }

        // Build switch statement
        $switch  = sprintf(
            "switch (%s) {\n%s\n", '$name', implode("\n", $caseStatements)
        );
        $switch .= sprintf(
            "%sdefault:\n%sreturn parent::get(%s, %s);\n", $indent, str_repeat($indent, 2), '$name', '$params'
        );
        $switch .= "}\n\n";

        // Build get() method
        $nameParam   = new CodeGen\ParameterGenerator();
        $nameParam->setName('name');
        $nameParam->setDefaultValue(array());
        $paramsParam = new CodeGen\ParameterGenerator();
        $paramsParam->setName('params')
            ->setType('array')
            ->setDefaultValue(array());

        $get = new CodeGen\MethodGenerator();
        $get->setName('get');
        $get->setParameters(array(
            $nameParam,
            $paramsParam,
        ));
        $get->setBody($switch);

        // Create class code generation object
        $container = new CodeGen\ClassGenerator();
        $container->setName($this->containerClass)
            ->setExtendedClass('ServiceLocator')
            ->setMethod($get)
            ->setMethods($getters);

        // Create PHP file code generation object
        $classFile = new CodeGen\FileGenerator();
        //$classFile->setUse('Humus\Di\ServiceLocator');
        $classFile->setClass($container);
        $classFile->setUse('Zend\Di\ServiceLocator');

        if (null !== $this->namespace) {
            $classFile->setNamespace($this->namespace);
        }

        if (null !== $filename) {
            $classFile->setFilename($filename);
        }

        return $classFile;
    }

    /**
     * Reduce aliases
     *
     * @param  string $name
     * @return string
     */
    protected function reduceAlias($name)
    {
        if (isset($this->aliases[$name])) {
            return $this->aliases[$name];
        }
        return $name;
    }

    /**
     * Create a PhpMethod code generation object named after a given alias
     *
     * @param  string $alias
     * @param  class $class Class to which alias refers
     * @return CodeGen\PhpMethod
     */
    protected function getCodeGenMethodFromAlias($alias, $class)
    {
        $alias = $this->normalizeAlias($alias);
        $method = new CodeGen\MethodGenerator();
        $method->setName($alias)
            ->setBody(sprintf('return $this->get(\'%s\');', $class));
        return $method;
    }

    /**
     * Normalize an alias to a getter method name
     *
     * @param  string $alias
     * @return string
     */
    protected function normalizeAlias($alias)
    {
        $normalized = preg_replace('/[^a-zA-Z0-9]/', ' ', $alias);
        $normalized = 'get' . str_replace(' ', '', ucwords($normalized));
        return $normalized;
    }
}
