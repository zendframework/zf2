<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace Zend\ServiceManager\Proxy;

use Doctrine\Common\Proxy\ProxyGenerator;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * Proxy generator responsible of creating proxy classes that delegate method calls to a wrapped service
 *
 * @category Zend
 * @package  Zend_ServiceManager
 * @author   Marco Pivetta <ocramius@gmail.com>
 */
class ServiceProxyGenerator extends ProxyGenerator
{
    const DEFAULT_SERVICE_PROXY_NS = 'Zend\\ServiceManager\\Proxy';

    /**
     * {@inheritDoc}
     *
     * @param string|null $proxyDir
     * @param string|null $proxyNs
     */
    public function __construct($proxyDir = null, $proxyNs = null)
    {
        $proxyDir = $proxyDir ?: sys_get_temp_dir();
        $proxyNs  = $proxyNs  ?: static::DEFAULT_SERVICE_PROXY_NS;

        parent::__construct($proxyDir, $proxyNs);

        $this->setPlaceholders(array(
            '<magicGet>'             => array($this, 'generateMagicGet'),
            '<magicSet>'             => array($this, 'generateMagicSet'),
            '<magicIsset>'           => array($this, 'generateMagicIsset'),
            '<sleepImpl>'            => '',
            '<wakeupImpl>'           => '',
            '<cloneImpl>'            => array($this, 'generateCloneImpl'),
            '<methods>'              => array($this, 'generateMethods'),
            '<additionalProperties>' => "\n    /**"
                . "\n     * @var object wrapped object to which method calls will be forwarded"
                . "\n     */"
                . "\n     public \$__wrappedObject__;",
        ));
    }

    /**
     * Generates the magic getter invoked when lazy loaded public properties are requested
     *
     * @param  ClassMetadata $class
     *
     * @return string
     */
    public function generateMagicGet(ClassMetadata $class)
    {
        return "    /**"
            . ($class->getReflectionClass()->hasMethod('__get') ? "\n     * {@inheritDoc}\n     *" : '')
            . "\n     * @param string \$name"
            . "\n     */"
            . "\n    public function __get(\$name)"
            . "\n    {\n"
            . "\n        \$this->__initializer__ && \$this->__initializer__->__invoke(\$this, '__get', array(\$name));"
            . "\n\n        return \$this->__wrappedObject__->\$name;"
            . "\n    }";
    }

    /**
     * Generates the magic setter (currently unused)
     *
     * @param  ClassMetadata $class
     *
     * @return string
     */
    public function generateMagicSet(ClassMetadata $class)
    {
        return "    /**"
            . ($class->getReflectionClass()->hasMethod('__set') ? "\n     * {@inheritDoc}\n     *" : '')
            . "\n     * @param string \$name"
            . "\n     * @param mixed  \$value"
            . "\n     */"
            . "\n    public function __set(\$name, \$value)"
            . "\n    {\n"
            . "\n        \$this->__initializer__ "
                . "&& \$this->__initializer__->__invoke(\$this, '__set', array(\$name, \$value));"
            . "\n\n        \$this->__wrappedObject__->\$name = \$value;"
            . "\n    }";
    }

    /**
     * Generates the magic issetter invoked when lazy loaded public properties are checked against isset()
     *
     * @param  ClassMetadata $class
     *
     * @return string
     */
    public function generateMagicIsset(ClassMetadata $class)
    {
        return "    /**"
            . ($class->getReflectionClass()->hasMethod('__isset') ? "\n     * {@inheritDoc}\n     *" : '')
            . "\n     * @param string \$name"
            . "\n     */"
            . "\n    public function __isset(\$name)"
            . "\n    {\n"
            . "\n        \$this->__initializer__ "
            . "&& \$this->__initializer__->__invoke(\$this, '__isset', array(\$name));"
            . "\n\n        return isset(\$this->__wrappedObject__->\$name);"
            . "\n    }";
    }

    /**
     * {@inheritDoc}
     */
    public function generateCloneImpl(ClassMetadata $class)
    {
        $hasClone = $class->getReflectionClass()->hasMethod('__clone');

        return "    /**"
            . ($hasClone ? "\n     * {@inheritDoc}" : "\n     *")
            . "\n     */"
            . "\n    public function __clone()"
            . "\n    {"
            . "\n        \$this->__initializer__ && \$this->__initializer__->__invoke(\$this, '__clone', array());"
            . "\n"
            . "\n        \$this->__wrappedObject__ = clone \$this->__wrappedObject__;"
            . "\n    }";
    }

    /**
     * {@inheritDoc}
     */
    public function generateMethods(ClassMetadata $class) {
        $methods            = '';
        $methodNames        = array();
        $reflectionMethods  = $class->getReflectionClass()->getMethods(\ReflectionMethod::IS_PUBLIC);
        $excludedMethods    = array(
            '__get'   => true,
            '__set'   => true,
            '__isset' => true,
            '__clone' => true,
        );

        foreach ($reflectionMethods as $method) {
            $name = $method->getName();

            if (
                $method->isConstructor()
                || isset($methodNames[$name])
                || isset($excludedMethods[strtolower($name)])
                || $method->isFinal()
                || $method->isStatic()
                || ! $method->isPublic()
            ) {
                continue;
            }

            $methodNames[$name] = true;
            $methods .= "\n    /**\n"
                . "     * {@inheritDoc}\n"
                . "     */\n"
                . '    public function ';

            if ($method->returnsReference()) {
                $methods .= '&';
            }

            $methods .= $name . '(';
            $firstParam = true;
            $parameterString = $argumentString = '';
            $parameters = array();

            foreach ($method->getParameters() as $param) {
                if ($firstParam) {
                    $firstParam = false;
                } else {
                    $parameterString .= ', ';
                    $argumentString  .= ', ';
                }

                $paramClass = $param->getClass();

                // We need to pick the type hint class too
                if (null !== $paramClass) {
                    $parameterString .= '\\' . $paramClass->getName() . ' ';
                } elseif ($param->isArray()) {
                    $parameterString .= 'array ';
                }

                if ($param->isPassedByReference()) {
                    $parameterString .= '&';
                }

                $parameters[] = '$' . $param->getName();
                $parameterString .= '$' . $param->getName();
                $argumentString  .= '$' . $param->getName();

                if ($param->isDefaultValueAvailable()) {
                    $parameterString .= ' = ' . var_export($param->getDefaultValue(), true);
                }
            }

            $methods .= $parameterString . ')';
            $methods .= "\n" . '    {' . "\n";

            $methods .= "        if(\$this->__initializer__) {\n"
                . "            \$cb = \$this->__initializer__;\n"
                . "            \$cb(\$this, " . var_export($name, true)
                . ", array(" . implode(', ', $parameters) . "));\n"
                . "        }\n\n"
                . '        return $this->__wrappedObject__->' . $name . '(' . $argumentString . ');'
                . "\n" . '    }' . "\n";
        }

        return $methods;
    }
}
