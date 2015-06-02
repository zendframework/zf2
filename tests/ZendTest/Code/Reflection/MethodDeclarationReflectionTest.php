<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @author    Daan Biesterbos <daanbiesterbos@gmail.com>
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection\MethodDeclarationReflection;
use Zend\Code\Reflection\MethodReflection;
use ZendTest\Code\Reflection\TestAsset\InjectableMethodReflection;

/**
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Method
 */
class MethodDeclarationReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testDeclaringInterfaceReturn()
    {
        $method = new MethodDeclarationReflection('ZendTest\Code\Reflection\TestAsset\TestSampleInterface1', 'doSomething');
        $this->assertInstanceOf('Zend\Code\Reflection\InterfaceReflection', $method->getDeclaringInterface());
    }

    public function testParameterReturn()
    {
        $method = new MethodDeclarationReflection('ZendTest\Code\Reflection\TestAsset\TestSampleInterface1', 'doSomething');
        $parameters = $method->getParameters();
        $this->assertEquals(5, count($parameters));
        $this->assertInstanceOf('Zend\Code\Reflection\ParameterReflection', array_shift($parameters));
    }

    public function testStartLine()
    {
        $reflectionMethod = new MethodDeclarationReflection('ZendTest\Code\Reflection\TestAsset\TestSampleInterface1', 'doSomething');

        $this->assertEquals(36, $reflectionMethod->getStartLine());
        $this->assertEquals(20, $reflectionMethod->getStartLine(true));
    }

    public function testGetPrototypeMethod()
    {
        $reflectionMethod = new MethodDeclarationReflection(
            'ZendTest\Code\Reflection\TestAsset\TestSampleInterface1',
            'doSomethingElse'
        );
        $prototype = array(
            'namespace' => 'ZendTest\Code\Reflection\TestAsset',
            'class' => 'TestSampleInterface1',
            'name' => 'doSomethingElse',
            'visibility' => 'public',
            'return' => 'int',
            'arguments' => array(
                'one' => array(
                    'type' => 'int',
                    'required' => true,
                    'by_ref' => false,
                    'default' => null,
                ),
                'two' => array(
                    'type' => 'int',
                    'required' => false,
                    'by_ref' => false,
                    'default' => 2,
                ),
                'three' => array(
                    'type' => 'string',
                    'required' => false,
                    'by_ref' => false,
                    'default' => 'three',
                ),
            ),
        );
        $this->assertEquals($prototype, $reflectionMethod->getPrototype());
        $this->assertEquals(
            'public int doSomethingElse(int $one, int $two = 2, string $three = \'three\');',
            $reflectionMethod->getPrototype(MethodDeclarationReflection::PROTOTYPE_AS_STRING)
        );

    }

    public function testGetAnnotationsWithNoNameInformations()
    {
        $reflectionMethod = new InjectableMethodReflection(
        // TestSampleClass5 has the annotations required to get to the
        // right point in the getAnnotations method.
            'ZendTest\Code\Reflection\TestAsset\TestSampleInterface1',
            'orDoNothingAtAll'
        );

        $annotationManager = new \Zend\Code\Annotation\AnnotationManager();

        $fileScanner = $this->getMockBuilder('Zend\Code\Scanner\CachingFileScanner')
                            ->disableOriginalConstructor()
                            ->getMock();

        $reflectionMethod->setFileScanner($fileScanner);

        $fileScanner->expects($this->any())
                    ->method('getClassNameInformation')
                    ->will($this->returnValue(false));

        $this->assertFalse($reflectionMethod->getAnnotations($annotationManager));
    }

}
