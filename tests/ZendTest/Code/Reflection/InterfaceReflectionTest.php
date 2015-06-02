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

use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\InterfaceReflection;
use ZendTest\Code\Reflection\TestAsset\InjectableClassReflection;

/**
 *
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Class
 */
class InterfaceReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testMethodReturns()
    {
        $reflectionInterface = new InterfaceReflection('ZendTest\Code\Reflection\TestAsset\TestSampleInterface3');

        $methodByName = $reflectionInterface->getMethod('doSomething');
        $this->assertInstanceOf('Zend\Code\Reflection\MethodDeclarationReflection', $methodByName);

        $methodsAll = $reflectionInterface->getMethods();
        $this->assertEquals(2, count($methodsAll));

        $firstMethod = array_shift($methodsAll);
        $this->assertEquals('doSomething', $firstMethod->getName());
    }


    public function testParentInterfacesReturn()
    {
        $reflectionInterface = new InterfaceReflection('ZendTest\Code\Reflection\TestAsset\TestSampleInterface3');
        $interfaces = $reflectionInterface->getParentInterfaces();
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals('ZendTest\Code\Reflection\TestAsset\TestSampleInterface4', $interfaces[0]->getName());
        $this->assertEquals('ZendTest\Code\Reflection\TestAsset\TestSampleInterface5', $interfaces[1]->getName());
    }

    public function testGetContentsReturnsContents()
    {
        $reflectionInterface = new InterfaceReflection('ZendTest\Code\Reflection\TestAsset\TestSampleInterface2');
        $target = <<<EOS
{
    /**
     * @param int \$one Description for one
     * @param int Description for two
     * @param string \$three Description for three
     *                      which spans multiple lines
     * @return mixed Some return descr
     */
    public function doSomething(\$one, \$two = 2, \$three = 'three', array \$array = array(), \$class = null);

    /**
     * @param int \$one Description for one
     * @param int Description for two
     * @param string \$three Description for three
     *                      which spans multiple lines
     * @return int
     */
    public function doSomethingElse(\$one, \$two = 2, \$three = 'three');
}
EOS;
        $contents = $reflectionInterface->getContents();
        $this->assertEquals(trim($target), trim($contents));
    }


    public function testStartLine()
    {
        $reflectionInterface = new InterfaceReflection('ZendTest\Code\Reflection\TestAsset\TestSampleInterface1');

        $this->assertEquals(18, $reflectionInterface->getStartLine());
        $this->assertEquals(5, $reflectionInterface->getStartLine(true));
    }

    public function testGetDeclaringFileReturnsFilename()
    {
        $reflectionInterface = new InterfaceReflection('ZendTest\Code\Reflection\TestAsset\TestSampleInterface1');
        $this->assertContains('TestSampleInterface1.php', $reflectionInterface->getDeclaringFile()->getFileName());
    }

    public function testGetAnnotationsWithNoNameInformations()
    {
        $reflectionInterface = new InjectableClassReflection(
            // TestSampleClass5 has the annotations required to get to the
            // right point in the getAnnotations method.
            'ZendTest\Code\Reflection\TestAsset\TestSampleInterface1'
        );

        $annotationManager = new \Zend\Code\Annotation\AnnotationManager();

        $fileScanner = $this->getMockBuilder('Zend\Code\Scanner\FileScanner')
                            ->disableOriginalConstructor()
                            ->getMock();

        $reflectionInterface->setFileScanner($fileScanner);

        $fileScanner->expects($this->any())
                    ->method('getClassNameInformation')
                    ->will($this->returnValue(false));

        $this->assertFalse($reflectionInterface->getAnnotations($annotationManager));
    }

    public function testGetContentsReturnsEmptyContentsOnEvaldCode()
    {
        $className = uniqid('InterfaceReflectionTestGenerated');

        eval('name' . 'space ' . __NAMESPACE__ . '; inter' . 'face ' . $className . '{}');

        $reflectionInterface = new InterfaceReflection(__NAMESPACE__ . '\\' . $className);

        $this->assertSame('', $reflectionInterface->getContents());
    }

    public function testGetContentsReturnsEmptyContentsOnInternalCode()
    {
        $reflectionInterface = new ClassReflection('Iterator');
        $this->assertSame('', $reflectionInterface->getContents());
    }


}
