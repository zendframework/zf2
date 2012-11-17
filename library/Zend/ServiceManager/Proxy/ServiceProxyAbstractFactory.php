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

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;

use Zend\Cache\Storage\StorageInterface;

use Doctrine\Common\Proxy\ProxyGenerator;
use Doctrine\Common\Proxy\Proxy;
use Doctrine\Common\Util\ClassUtils;

/**
 * Abstract Service Factory responsible of generating lazy service instances that double
 * the functionality of the actually requested ones.
 *
 * @category Zend
 * @package  Zend_ServiceManager
 * @author   Marco Pivetta <ocramius@gmail.com>
 */
class ServiceProxyAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var ProxyGenerator
     */
    private $proxyGenerator;

    /**
     * @var StorageInterface used to store the proxy definitions
     */
    private $cache;

    /**
     * @var string
     */
    protected $proxyNamespace = 'Zend\\ServiceManager\\Proxy';

    public function __construct(StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $serviceName, $requestedName)
    {
        /* @var $serviceLocator ServiceManager */

        // FQCN is cached since we don't know anything about the requested service, and want to avoid instantiation
        if (!$fqcn = $this->cache->getItem($requestedName)) {
            $service = $serviceLocator->create($requestedName);
            $fqcn    = ClassUtils::generateProxyClassName(get_class($service), $this->proxyNamespace);
            $this->cache->setItem($requestedName, $fqcn);
        }

        if (class_exists($fqcn)) {
            return new $fqcn(
                function (Proxy $proxy) use ($serviceLocator, $serviceName, $requestedName) {
                    $proxy->__setInitializer(null);
                    $proxy->__setInitialized(true);
                    $proxy->__wrappedObject__ = $serviceLocator->create($serviceName);
                },
                null
            );
        }

        $service        = isset($service) ? $service : $serviceLocator->create($requestedName);
        $className      = get_class($service);
        $proxyGenerator = $this->getProxyGenerator();

        $proxyGenerator->generateProxyClass(new ServiceClassMetadata($className));
        require_once $proxyGenerator->getProxyFileName($className);

        $proxy = new $fqcn(null, null);
        $proxy->__wrappedObject__ = $service;
        $proxy->__setInitialized(true);

        return $proxy;
    }

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $serviceLocator instanceof ServiceManager;
    }

    /**
     * @param ProxyGenerator $proxyGenerator
     */
    public function setProxyGenerator(ProxyGenerator $proxyGenerator)
    {
        $this->proxyGenerator = $proxyGenerator;
    }

    /**
     * @return ProxyGenerator
     */
    public function getProxyGenerator()
    {
        if (null === $this->proxyGenerator) {
            $this->proxyGenerator = new ProxyGenerator(__DIR__ . '/tests', $this->proxyNamespace);
            $this->proxyGenerator->setPlaceholder(
                '<additionalProperties>',
                "\n    /**"
                    . "\n     * @var object wrapped object to which method calls will be forwarded"
                    . "\n     */"
                    . "\n     public \$__wrappedObject__;"
            );
            $this->proxyGenerator->setPlaceholder(
                '<methods>',
                function (ServiceClassMetadata $class) {
                    $methods = '';
                    $methodNames = array();
                    $reflectionMethods = $class->getReflectionClass()->getMethods(\ReflectionMethod::IS_PUBLIC);
                    $skippedMethods = array(
                        '__sleep'   => true,
                        '__clone'   => true,
                        '__wakeup'  => true,
                        '__get'     => true,
                        '__set'     => true,
                        '__isset'   => true,
                    );

                    foreach ($reflectionMethods as $method) {
                        $name = $method->getName();

                        if (
                            $method->isConstructor()
                            || isset($skippedMethods[strtolower($name)])
                            || isset($methodNames[$name])
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
            );
        }

        return $this->proxyGenerator;
    }
}
