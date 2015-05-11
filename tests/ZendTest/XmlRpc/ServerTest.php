<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\XmlRpc;

use Zend\XmlRpc\Server;
use Zend\XmlRpc\Request;
use Zend\XmlRpc\Response;
use Zend\XmlRpc\AbstractValue;
use Zend\XmlRpc\Value;
use Zend\XmlRpc\Fault;
use Zend\XmlRpc;

/**
 * @group      Zend_XmlRpc
 */
class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Server object
     * @var Server
     */
    protected $_server;

    /**
     * Setup environment
     */
    public function setUp()
    {
        $this->_server = new Server();
        $this->_server->setReturnResponse(true);
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        unset($this->_server);
    }

    /**
     * __construct() test
     *
     * Call as method call
     *
     * Returns: void
     */
    public function test__construct()
    {
        $this->assertInstanceOf('Zend\XmlRpc\Server', $this->_server);
    }

    public function suppressNotFoundWarnings($errno, $errstr)
    {
        if (!strstr($errstr, 'failed')) {
            return false;
        }
    }

    /**
     * addFunction() test
     *
     * Call as method call
     *
     * Expects:
     * - function:
     * - namespace: Optional; has default;
     *
     * Returns: void
     */
    public function testAddFunction()
    {
        $this->_server->addFunction('ZendTest\\XmlRpc\\testFunction', 'zsr');

        $methods = $this->_server->listMethods();
        $this->assertContains('zsr.ZendTest\\XmlRpc\\testFunction', $methods, var_export($methods, 1));

        $methods = $this->_server->listMethods();
        $this->assertContains('zsr.ZendTest\\XmlRpc\\testFunction', $methods);
        $this->assertNotContains('zsr.ZendTest\\XmlRpc\\testFunction2', $methods, var_export($methods, 1));
    }

    public function testAddFunctionThrowsExceptionOnInvalidInput()
    {
        $this->setExpectedException('Zend\XmlRpc\Server\Exception\InvalidArgumentException', 'Unable to attach function; invalid');
        $this->_server->addFunction('nosuchfunction');
    }

    public function testAddFunctionThrowsExceptionOnInvalidArrayInput()
    {
        //$this->setExpectedException('XXX', 'xxx');
        $this->_server->addFunction(
            [
                'ZendTest\\XmlRpc\\testFunction',
                'ZendTest\\XmlRpc\\testFunction2',
            ],
            'zsr'
        );
    }

    /**
     * getReturnResponse() default value
     */
    public function testEmitResponseByDefault()
    {
        $server = new Server();

        $this->assertFalse($server->getReturnResponse());
    }

    /**
     * get/loadFunctions() test
     */
    public function testFunctions()
    {
        $expected = $this->_server->listMethods();

        $functions = $this->_server->getFunctions();
        $server = new Server();
        $server->loadFunctions($functions);
        $actual = $server->listMethods();

        $this->assertSame($expected, $actual);
    }

    /**
     * setClass() test
     */
    public function testSetClass()
    {
        $this->_server->setClass('ZendTest\\XmlRpc\\TestClass', 'test');
        $methods = $this->_server->listMethods();
        $this->assertContains('test.test1', $methods);
        $this->assertContains('test.test2', $methods);
        $this->assertNotContains('test._test3', $methods);
        $this->assertNotContains('test.__construct', $methods);
    }

    /**
     * @group ZF-6526
     */
    public function testSettingClassWithArguments()
    {
        $this->_server->setClass('ZendTest\\XmlRpc\\TestClass', 'test', 'argv-argument');
        $this->assertTrue($this->_server->sendArgumentsToAllMethods());
        $request = new Request();
        $request->setMethod('test.test4');
        $response = $this->_server->handle($request);
        $this->assertNotInstanceOf('Zend\\XmlRpc\\Fault', $response);
        $this->assertSame(
            ['test1' => 'argv-argument',
                'test2' => null,
                'arg' => ['argv-argument']],
            $response->getReturnValue());
    }

    public function testSettingClassWithArgumentsOnlyPassingToConstructor()
    {
        $this->_server->setClass('ZendTest\\XmlRpc\\TestClass', 'test', 'a1', 'a2');
        $this->_server->sendArgumentsToAllMethods(false);
        $this->assertFalse($this->_server->sendArgumentsToAllMethods());

        $request = new Request();
        $request->setMethod('test.test4');
        $request->setParams(['foo']);
        $response = $this->_server->handle($request);
        $this->assertNotInstanceOf('Zend\\XmlRpc\\Fault', $response);
        $this->assertSame(['test1' => 'a1', 'test2' => 'a2', 'arg' => ['foo']], $response->getReturnValue());
    }

    /**
     * fault() test
     */
    public function testFault()
    {
        $fault = $this->_server->fault('This is a fault', 411);
        $this->assertInstanceOf('Zend\XmlRpc\Server\Fault', $fault);
        $this->assertEquals(411, $fault->getCode());
        $this->assertEquals('This is a fault', $fault->getMessage());

        $fault = $this->_server->fault(new Server\Exception\RuntimeException('Exception fault', 511));
        $this->assertInstanceOf('Zend\XmlRpc\Server\Fault', $fault);
        $this->assertEquals(511, $fault->getCode());
        $this->assertEquals('Exception fault', $fault->getMessage());
    }

    /**
     * handle() test - default behavior should be to not return the response
     */
    public function testHandle()
    {
        $request = new Request();
        $request->setMethod('system.listMethods');
        $this->_server->setReturnResponse(false);
        ob_start();
        $response = $this->_server->handle($request);
        $output = ob_get_contents();
        ob_end_clean();
        $this->_server->setReturnResponse(true);

        $this->assertFalse(isset($response));
        $response = $this->_server->getResponse();
        $this->assertInstanceOf('Zend\XmlRpc\Response', $response);
        $this->assertSame($response->__toString(), $output);
        $return = $response->getReturnValue();
        $this->assertInternalType('array', $return);
        $this->assertContains('system.multicall', $return);
    }

    /**
     * handle() test
     *
     * Call as method call
     *
     * Expects:
     * - request: Optional;
     *
     * Returns: Zend_XmlRpc_Response|Zend_XmlRpc_Fault
     */
    public function testHandleWithReturnResponse()
    {
        $request = new Request();
        $request->setMethod('system.listMethods');
        $response = $this->_server->handle($request);

        $this->assertInstanceOf('Zend\XmlRpc\Response', $response);
        $return = $response->getReturnValue();
        $this->assertInternalType('array', $return);
        $this->assertContains('system.multicall', $return);
    }

    /**
     * Test that only calling methods using a valid parameter signature works
     */
    public function testHandle2()
    {
        $request = new Request();
        $request->setMethod('system.methodHelp');
        $response = $this->_server->handle($request);

        $this->assertInstanceOf('Zend\XmlRpc\Fault', $response);
        $this->assertEquals(623, $response->getCode());
    }

    public function testCallingInvalidMethod()
    {
        $request = new Request();
        $request->setMethod('invalid');
        $response = $this->_server->handle($request);
        $this->assertInstanceOf('Zend\\XmlRpc\\Fault', $response);
        $this->assertSame('Method "invalid" does not exist', $response->getMessage());
        $this->assertSame(620, $response->getCode());
    }


    /**
     * setResponseClass() test
     *
     * Call as method call
     *
     * Expects:
     * - class:
     *
     * Returns: bool
     */
    public function testSetResponseClass()
    {
        $this->assertTrue($this->_server->setResponseClass('ZendTest\\XmlRpc\\TestResponse'));
        $request = new Request();
        $request->setMethod('system.listMethods');
        $response = $this->_server->handle($request);

        $this->assertInstanceOf('Zend\XmlRpc\Response', $response);
        $this->assertInstanceOf('ZendTest\XmlRpc\TestResponse', $response);
    }

    /**
     * listMethods() test
     *
     * Call as method call
     *
     * Returns: array
     */
    public function testListMethods()
    {
        $methods = $this->_server->listMethods();
        $this->assertInternalType('array', $methods);
        $this->assertContains('system.listMethods', $methods);
        $this->assertContains('system.methodHelp', $methods);
        $this->assertContains('system.methodSignature', $methods);
        $this->assertContains('system.multicall', $methods);
    }

    /**
     * methodHelp() test
     *
     * Call as method call
     *
     * Expects:
     * - method:
     *
     * Returns: string
     */
    public function testMethodHelp()
    {
        $help = $this->_server->methodHelp('system.methodHelp', 'system.listMethods');
        $this->assertContains('Display help message for an XMLRPC method', $help);

        $this->setExpectedException('Zend\\XmlRpc\\Server\\Exception\\ExceptionInterface', 'Method "foo" does not exist');
        $this->_server->methodHelp('foo');
    }

    /**
     * methodSignature() test
     *
     * Call as method call
     *
     * Expects:
     * - method:
     *
     * Returns: array
     */
    public function testMethodSignature()
    {
        $sig = $this->_server->methodSignature('system.methodSignature');
        $this->assertInternalType('array', $sig);
        $this->assertEquals(1, count($sig), var_export($sig, 1));

        $this->setExpectedException('Zend\XmlRpc\Server\Exception\ExceptionInterface', 'Method "foo" does not exist');
        $this->_server->methodSignature('foo');
    }

    /**
     * multicall() test
     *
     * Call as method call
     *
     * Expects:
     * - methods:
     *
     * Returns: array
     */
    public function testMulticall()
    {
        $struct = [
            [
                'methodName' => 'system.listMethods',
                'params' => []
            ],
            [
                'methodName' => 'system.methodHelp',
                'params' => ['system.multicall']
            ]
        ];
        $request = new Request();
        $request->setMethod('system.multicall');
        $request->addParam($struct);
        $response = $this->_server->handle($request);

        $this->assertInstanceOf('Zend\XmlRpc\Response', $response, $response->__toString() . "\n\n" . $request->__toString());
        $returns = $response->getReturnValue();
        $this->assertInternalType('array', $returns);
        $this->assertEquals(2, count($returns), var_export($returns, 1));
        $this->assertInternalType('array', $returns[0], var_export($returns[0], 1));
        $this->assertInternalType('string', $returns[1], var_export($returns[1], 1));
    }

    /**
     * @group ZF-5635
     */
    public function testMulticallHandlesFaults()
    {
        $struct = [
            [
                'methodName' => 'system.listMethods',
                'params' => []
            ],
            [
                'methodName' => 'undefined',
                'params' => []
            ]
        ];
        $request = new Request();
        $request->setMethod('system.multicall');
        $request->addParam($struct);
        $response = $this->_server->handle($request);

        $this->assertInstanceOf('Zend\XmlRpc\Response', $response, $response->__toString() . "\n\n" . $request->__toString());
        $returns = $response->getReturnValue();
        $this->assertInternalType('array', $returns);
        $this->assertEquals(2, count($returns), var_export($returns, 1));
        $this->assertInternalType('array', $returns[0], var_export($returns[0], 1));
        $this->assertSame([
            'faultCode' => 620, 'faultString' => 'Method "undefined" does not exist'],
            $returns[1], var_export($returns[1], 1));
    }

    /**
     * Test get/setEncoding()
     */
    public function testGetSetEncoding()
    {
        $this->assertEquals('UTF-8', $this->_server->getEncoding());
        $this->assertEquals('UTF-8', AbstractValue::getGenerator()->getEncoding());
        $this->assertSame($this->_server, $this->_server->setEncoding('ISO-8859-1'));
        $this->assertEquals('ISO-8859-1', $this->_server->getEncoding());
        $this->assertEquals('ISO-8859-1', AbstractValue::getGenerator()->getEncoding());
    }

    /**
     * Test request/response encoding
     */
    public function testRequestResponseEncoding()
    {
        $response = $this->_server->handle();
        $request  = $this->_server->getRequest();

        $this->assertEquals('UTF-8', $request->getEncoding());
        $this->assertEquals('UTF-8', $response->getEncoding());
    }

    /**
     * Test request/response encoding (alternate encoding)
     */
    public function testRequestResponseEncoding2()
    {
        $this->_server->setEncoding('ISO-8859-1');
        $response = $this->_server->handle();
        $request  = $this->_server->getRequest();

        $this->assertEquals('ISO-8859-1', $request->getEncoding());
        $this->assertEquals('ISO-8859-1', $response->getEncoding());
    }

    public function testAddFunctionWithExtraArgs()
    {
        $this->_server->addFunction('ZendTest\\XmlRpc\\testFunction', 'test', 'arg1');
        $methods = $this->_server->listMethods();
        $this->assertContains('test.ZendTest\\XmlRpc\\testFunction', $methods);
    }

    public function testAddFunctionThrowsExceptionWithBadData()
    {
        $o = new \stdClass();
        $this->setExpectedException('Zend\XmlRpc\Server\Exception\InvalidArgumentException', 'Unable to attach function; invalid');
        $this->_server->addFunction($o);
    }

    public function testLoadFunctionsThrowsExceptionWithBadData()
    {
        $o = new \stdClass();
        $this->setExpectedException('Zend\XmlRpc\Server\Exception\InvalidArgumentException', 'Unable to load server definition; must be an array or Zend\Server\Definition, received stdClass');
        $this->_server->loadFunctions($o);
    }

    public function testLoadFunctionsThrowsExceptionsWithBadData2()
    {
        $this->setExpectedException('Zend\XmlRpc\Server\Exception\InvalidArgumentException', 'Unable to load server definition; must be an array or Zend\Server\Definition, received string');
        $this->_server->loadFunctions('foo');
    }

    public function testLoadFunctionsThrowsExceptionsWithBadData3()
    {
        $o = new \stdClass();
        $o = [$o];

        $this->setExpectedException('Zend\Server\Exception\InvalidArgumentException', 'Invalid method provided');
        $this->_server->loadFunctions($o);
    }

    public function testLoadFunctionsReadsMethodsFromServerDefinitionObjects()
    {
        $mockedMethod = $this->getMock('Zend\\Server\\Method\\Definition', [], [], '', false,
            false);
        $mockedDefinition = $this->getMock('Zend\\Server\\Definition', [], [], '', false, false);
        $mockedDefinition->expects($this->once())
                         ->method('getMethods')
                         ->will($this->returnValue(['bar' => $mockedMethod]));
        $this->_server->loadFunctions($mockedDefinition);
    }

    public function testSetClassThrowsExceptionWithInvalidClass()
    {
        $this->setExpectedException('Zend\XmlRpc\Server\Exception\InvalidArgumentException', 'Invalid method class');
        $this->_server->setClass('mybogusclass');
    }

    public function testSetRequestUsingString()
    {
        $this->_server->setRequest('ZendTest\\XmlRpc\\TestRequest');
        $req = $this->_server->getRequest();
        $this->assertInstanceOf('ZendTest\XmlRpc\TestRequest', $req);
    }

    /**
     * ////////@outputBuffering enabled
     */
    public function testSetRequestThrowsExceptionOnBadClassName()
    {
        $this->setExpectedException('Zend\XmlRpc\Server\Exception\InvalidArgumentException', 'Invalid request object');
        $this->_server->setRequest('ZendTest\\XmlRpc\\TestRequest2');
    }

    public function testSetRequestThrowsExceptionOnBadObject()
    {
        $this->setExpectedException('Zend\XmlRpc\Server\Exception\InvalidArgumentException', 'Invalid request object');
        $this->_server->setRequest($this);
    }

    public function testHandleObjectMethod()
    {
        $this->_server->setClass('ZendTest\\XmlRpc\\TestClass');
        $request = new Request();
        $request->setMethod('test1');
        $request->addParam('value');
        $response = $this->_server->handle($request);
        $this->assertNotInstanceOf('Zend\XmlRpc\Fault', $response);
        $this->assertEquals('String: value', $response->getReturnValue());
    }

    public function testHandleClassStaticMethod()
    {
        $this->_server->setClass('ZendTest\\XmlRpc\\TestClass');
        $request = new Request();
        $request->setMethod('test2');
        $request->addParam(['value1', 'value2']);
        $response = $this->_server->handle($request);
        $this->assertNotInstanceOf('Zend\XmlRpc\Fault', $response);
        $this->assertEquals('value1; value2', $response->getReturnValue());
    }

    public function testHandleFunction()
    {
        $this->_server->addFunction('ZendTest\\XmlRpc\\testFunction');
        $request = new Request();
        $request->setMethod('ZendTest\\XmlRpc\\testFunction');
        $request->setParams([['value1'], 'key']);
        $response = $this->_server->handle($request);
        $this->assertNotInstanceOf('Zend\XmlRpc\Fault', $response);
        $this->assertEquals('key: value1', $response->getReturnValue());
    }

    public function testMulticallReturnsFaultsWithBadData()
    {
        // bad method array
        $try = [
            'system.listMethods',
            [
                'name' => 'system.listMethods'
            ],
            [
                'methodName' => 'system.listMethods'
            ],
            [
                'methodName' => 'system.listMethods',
                'params'     => ''
            ],
            [
                'methodName' => 'system.multicall',
                'params'     => []
            ]
        ];
        $returned = $this->_server->multicall($try);
        $this->assertInternalType('array', $returned);
        $this->assertEquals(5, count($returned));

        $response = $returned[0];
        $this->assertInternalType('array', $response);
        $this->assertTrue(isset($response['faultCode']));
        $this->assertEquals(601, $response['faultCode']);

        $response = $returned[1];
        $this->assertInternalType('array', $response);
        $this->assertTrue(isset($response['faultCode']));
        $this->assertEquals(602, $response['faultCode']);

        $response = $returned[2];
        $this->assertInternalType('array', $response);
        $this->assertTrue(isset($response['faultCode']));
        $this->assertEquals(603, $response['faultCode']);

        $response = $returned[3];
        $this->assertInternalType('array', $response);
        $this->assertTrue(isset($response['faultCode']));
        $this->assertEquals(604, $response['faultCode']);

        $response = $returned[4];
        $this->assertInternalType('array', $response);
        $this->assertTrue(isset($response['faultCode']));
        $this->assertEquals(605, $response['faultCode']);
    }

    /**
     * @group ZF-2872
     */
    public function testCanMarshalBase64Requests()
    {
        $this->_server->setClass('ZendTest\\XmlRpc\\TestClass', 'test');
        $data    = base64_encode('this is the payload');
        $param   = ['type' => 'base64', 'value' => $data];
        $request = new Request('test.base64', [$param]);

        $response = $this->_server->handle($request);
        $this->assertNotInstanceOf('Zend\XmlRpc\Fault', $response);
        $this->assertEquals($data, $response->getReturnValue());
    }

    /**
     * @group ZF-6034
     */
    public function testPrototypeReturnValueMustReflectDocBlock()
    {
        $server = new Server();
        $server->setClass('ZendTest\\XmlRpc\\TestClass');
        $table = $server->getDispatchTable();
        $method = $table->getMethod('test1');
        foreach ($method->getPrototypes() as $prototype) {
            $this->assertNotEquals('void', $prototype->getReturnType(), var_export($prototype, 1));
        }
    }

    public function testCallingUnregisteredMethod()
    {
        $this->setExpectedException('Zend\\XmlRpc\\Server\\Exception\\ExceptionInterface',
            'Unknown instance method called on server: foobarbaz');
        $this->_server->foobarbaz();
    }

    public function testSetPersistenceDoesNothing()
    {
        $this->assertNull($this->_server->setPersistence('foo'));
        $this->assertNull($this->_server->setPersistence('whatever'));
    }

    public function testPassingInvalidRequestClassThrowsException()
    {
        $this->setExpectedException('Zend\\XmlRpc\\Server\\Exception\\ExceptionInterface', 'Invalid request class');
        $this->_server->setRequest('stdClass');
    }

    public function testPassingInvalidResponseClassThrowsException()
    {
        $this->setExpectedException('Zend\\XmlRpc\\Server\\Exception\\ExceptionInterface', 'Invalid response class');
        $this->_server->setResponseClass('stdClass');
    }

    public function testCreatingFaultWithEmptyMessageResultsInUnknownError()
    {
        $fault = $this->_server->fault('', 123);
        $this->assertSame('Unknown Error', $fault->getMessage());
        $this->assertSame(123, $fault->getCode());
    }
}

/**
 * testFunction
 *
 * Function for use with xmlrpc server unit tests
 *
 * @param array $var1
 * @param string $var2
 * @return string
 */
function testFunction($var1, $var2 = 'optional')
{
    return $var2 . ': ' . implode(',', (array) $var1);
}

/**
 * testFunction2
 *
 * Function for use with xmlrpc server unit tests
 *
 * @return string
 */
function testFunction2()
{
    return 'function2';
}


class TestClass
{
    private $_value1;
    private $_value2;

    /**
     * Constructor
     *
     */
    public function __construct($value1 = null, $value2 = null)
    {
        $this->_value1 = $value1;
        $this->_value2 = $value2;
    }

    /**
     * Test1
     *
     * Returns 'String: ' . $string
     *
     * @param string $string
     * @return string
     */
    public function test1($string)
    {
        return 'String: ' . (string) $string;
    }

    /**
     * Test2
     *
     * Returns imploded array
     *
     * @param array $array
     * @return string
     */
    public static function test2($array)
    {
        return implode('; ', (array) $array);
    }

    /**
     * Test3
     *
     * Should not be available...
     *
     * @return void
     */
    protected function _test3()
    {
    }

    /**
     * @param string $arg
     * @return struct
     */
    public function test4($arg)
    {
        return ['test1' => $this->_value1, 'test2' => $this->_value2, 'arg' => func_get_args()];
    }

    /**
     * Test base64 encoding in request and response
     *
     * @param  base64 $data
     * @return base64
     */
    public function base64($data)
    {
        return $data;
    }
}

class TestResponse extends Response
{
}

class TestRequest extends Request
{
}
