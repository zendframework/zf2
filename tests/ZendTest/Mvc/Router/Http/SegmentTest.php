<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request;
use Zend\I18n\Translator\TextDomain;
use Zend\I18n\Translator\Translator;
use Zend\Stdlib\Request as BaseRequest;
use Zend\Mvc\Router\Http\Segment;
use ZendTest\I18n\Translator\TestAsset\Loader as TestLoader;
use ZendTest\Mvc\Router\FactoryTester;

class SegmentTest extends TestCase
{
    public static function routeProvider()
    {
        $translator = new Translator();
        $translator->setLocale('en-US');
        $enLoader     = new TestLoader();
        $deLoader     = new TestLoader();
        $domainLoader = new TestLoader();
        $enLoader->textDomain     = new TextDomain(['fw' => 'framework']);
        $deLoader->textDomain     = new TextDomain(['fw' => 'baukasten']);
        $domainLoader->textDomain = new TextDomain(['fw' => 'fw-alternative']);
        $translator->getPluginManager()->setService('test-en',     $enLoader);
        $translator->getPluginManager()->setService('test-de',     $deLoader);
        $translator->getPluginManager()->setService('test-domain', $domainLoader);
        $translator->addTranslationFile('test-en', null, 'default', 'en-US');
        $translator->addTranslationFile('test-de', null, 'default', 'de-DE');
        $translator->addTranslationFile('test-domain', null, 'alternative', 'en-US');

        return [
            'simple-match' => [
                new Segment('/:foo'),
                '/bar',
                null,
                ['foo' => 'bar']
            ],
            'no-match-without-leading-slash' => [
                new Segment(':foo'),
                '/bar/',
                null,
                null
            ],
            'no-match-with-trailing-slash' => [
                new Segment('/:foo'),
                '/bar/',
                null,
                null
            ],
            'offset-skips-beginning' => [
                new Segment(':foo'),
                '/bar',
                1,
                ['foo' => 'bar']
            ],
            'offset-enables-partial-matching' => [
                new Segment('/:foo'),
                '/bar/baz',
                0,
                ['foo' => 'bar']
            ],
            'match-overrides-default' => [
                new Segment('/:foo', [], ['foo' => 'baz']),
                '/bar',
                null,
                ['foo' => 'bar']
            ],
            'constraints-prevent-match' => [
                new Segment('/:foo', ['foo' => '\d+']),
                '/bar',
                null,
                null
            ],
            'constraints-allow-match' => [
                new Segment('/:foo', ['foo' => '\d+']),
                '/123',
                null,
                ['foo' => '123']
            ],
            'constraints-override-non-standard-delimiter' => [
                new Segment('/:foo{-}/bar', ['foo' => '[^/]+']),
                '/foo-bar/bar',
                null,
                ['foo' => 'foo-bar']
            ],
            'constraints-with-parantheses-dont-break-parameter-map' => [
                new Segment('/:foo/:bar', ['foo' => '(bar)']),
                '/bar/baz',
                null,
                ['foo' => 'bar', 'bar' => 'baz']
            ],
            'simple-match-with-optional-parameter' => [
                new Segment('/[:foo]', [], ['foo' => 'bar']),
                '/',
                null,
                ['foo' => 'bar']
            ],
            'optional-parameter-is-ignored' => [
                new Segment('/:foo[/:bar]'),
                '/bar',
                null,
                ['foo' => 'bar']
            ],
            'optional-parameter-is-provided-with-default' => [
                new Segment('/:foo[/:bar]', [], ['bar' => 'baz']),
                '/bar',
                null,
                ['foo' => 'bar', 'bar' => 'baz']
            ],
            'optional-parameter-is-consumed' => [
                new Segment('/:foo[/:bar]'),
                '/bar/baz',
                null,
                ['foo' => 'bar', 'bar' => 'baz']
            ],
            'optional-group-is-discared-with-missing-parameter' => [
                new Segment('/:foo[/:bar/:baz]', [], ['bar' => 'baz']),
                '/bar',
                null,
                ['foo' => 'bar', 'bar' => 'baz']
            ],
            'optional-group-within-optional-group-is-ignored' => [
                new Segment('/:foo[/:bar[/:baz]]', [], ['bar' => 'baz', 'baz' => 'bat']),
                '/bar',
                null,
                ['foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat']
            ],
            'non-standard-delimiter-before-parameter' => [
                new Segment('/foo-:bar'),
                '/foo-baz',
                null,
                ['bar' => 'baz']
            ],
            'non-standard-delimiter-between-parameters' => [
                new Segment('/:foo{-}-:bar'),
                '/bar-baz',
                null,
                ['foo' => 'bar', 'bar' => 'baz']
            ],
            'non-standard-delimiter-before-optional-parameter' => [
                new Segment('/:foo{-/}[-:bar]/:baz'),
                '/bar-baz/bat',
                null,
                ['foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat']
            ],
            'non-standard-delimiter-before-ignored-optional-parameter' => [
                new Segment('/:foo{-/}[-:bar]/:baz'),
                '/bar/bat',
                null,
                ['foo' => 'bar', 'baz' => 'bat']
            ],
            'parameter-with-dash-in-name' => [
                new Segment('/:foo-bar'),
                '/baz',
                null,
                ['foo-bar' => 'baz']
            ],
            'url-encoded-parameters-are-decoded' => [
                new Segment('/:foo'),
                '/foo%20bar',
                null,
                ['foo' => 'foo bar']
            ],
            'urlencode-flaws-corrected' => [
                new Segment('/:foo'),
                "/!$&'()*,-.:;=@_~+",
                null,
                ['foo' => "!$&'()*,-.:;=@_~+"]
            ],
            'empty-matches-are-replaced-with-defaults' => [
                new Segment('/foo[/:bar]/baz-:baz', [], ['bar' => 'bar']),
                '/foo/baz-baz',
                null,
                ['bar' => 'bar', 'baz' => 'baz']
            ],
            'translate-with-default-locale' => [
                new Segment('/{fw}', [], []),
                '/framework',
                null,
                [],
                ['translator' => $translator]
            ],
            'translate-with-specific-locale' => [
                new Segment('/{fw}', [], []),
                '/baukasten',
                null,
                [],
                ['translator' => $translator, 'locale' => 'de-DE']
            ],
            'translate-uses-message-id-as-fallback' => [
                new Segment('/{fw}', [], []),
                '/fw',
                null,
                [],
                ['translator' => $translator, 'locale' => 'fr-FR']
            ],
            'translate-with-specific-text-domain' => [
                new Segment('/{fw}', [], []),
                '/fw-alternative',
                null,
                [],
                ['translator' => $translator, 'text_domain' => 'alternative']
            ],
        ];
    }

    public static function parseExceptionsProvider()
    {
        return [
            'unbalanced-brackets' => [
                '[',
                'Zend\Mvc\Router\Exception\RuntimeException',
                'Found unbalanced brackets'
            ],
            'closing-bracket-without-opening-bracket' => [
                ']',
                'Zend\Mvc\Router\Exception\RuntimeException',
                'Found closing bracket without matching opening bracket'
            ],
            'empty-parameter-name' => [
                ':',
                'Zend\Mvc\Router\Exception\RuntimeException',
                'Found empty parameter name'
            ],
            'translated-literal-without-closing-backet' => [
                '{test',
                'Zend\Mvc\Router\Exception\RuntimeException',
                'Translated literal missing closing bracket'
            ],
        ];
    }

    /**
     * @dataProvider routeProvider
     * @param        Segment $route
     * @param        string  $path
     * @param        integer $offset
     * @param        array   $params
     * @param        array   $options
     */
    public function testMatching(Segment $route, $path, $offset, array $params = null, array $options = [])
    {
        $request = new Request();
        $request->setUri('http://example.com' . $path);
        $match = $route->match($request, $offset, $options);

        if ($params === null) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);

            if ($offset === null) {
                $this->assertEquals(strlen($path), $match->getLength());
            }

            foreach ($params as $key => $value) {
                $this->assertEquals($value, $match->getParam($key));
            }
        }
    }

    /**
     * @dataProvider routeProvider
     * @param        Segment $route
     * @param        string  $path
     * @param        integer $offset
     * @param        array   $params
     * @param        array   $options
     */
    public function testAssembling(Segment $route, $path, $offset, array $params = null, array $options = [])
    {
        if ($params === null) {
            // Data which will not match are not tested for assembling.
            return;
        }

        $result = $route->assemble($params, $options);

        if ($offset !== null) {
            $this->assertEquals($offset, strpos($path, $result, $offset));
        } else {
            $this->assertEquals($path, $result);
        }
    }

    /**
     * @dataProvider parseExceptionsProvider
     * @param        string $route
     * @param        string $exceptionName
     * @param        string $exceptionMessage
     */
    public function testParseExceptions($route, $exceptionName, $exceptionMessage)
    {
        $this->setExpectedException($exceptionName, $exceptionMessage);
        new Segment($route);
    }

    public function testAssemblingWithMissingParameterInRoot()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Missing parameter "foo"');
        $route = new Segment('/:foo');
        $route->assemble();
    }

    public function testTranslatedAssemblingThrowsExceptionWithoutTranslator()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'No translator provided');
        $route = new Segment('/{foo}');
        $route->assemble();
    }

    public function testTranslatedMatchingThrowsExceptionWithoutTranslator()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'No translator provided');
        $route = new Segment('/{foo}');
        $route->match(new Request());
    }

    public function testNoMatchWithoutUriMethod()
    {
        $route   = new Segment('/foo');
        $request = new BaseRequest();

        $this->assertNull($route->match($request));
    }

    public function testAssemblingWithExistingChild()
    {
        $route = new Segment('/[:foo]', [], ['foo' => 'bar']);
        $path = $route->assemble([], ['has_child' => true]);

        $this->assertEquals('/bar', $path);
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Segment',
            [
                'route' => 'Missing "route" in options array'
            ],
            [
                'route'       => '/:foo[/:bar{-}]',
                'constraints' => ['foo' => 'bar']
            ]
        );
    }

    public function testRawDecode()
    {
        // verify all characters which don't absolutely require encoding pass through match unchanged
        // this includes every character other than #, %, / and ?
        $raw = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789`-=[]\\;\',.~!@$^&*()_+{}|:"<>';
        $request = new Request();
        $request->setUri('http://example.com/' . $raw);
        $route   = new Segment('/:foo');
        $match   = $route->match($request);

        $this->assertSame($raw, $match->getParam('foo'));
    }

    public function testEncodedDecode()
    {
        // every character
        $in  = '%61%62%63%64%65%66%67%68%69%6a%6b%6c%6d%6e%6f%70%71%72%73%74%75%76%77%78%79%7a%41%42%43%44%45%46%47%48%49%4a%4b%4c%4d%4e%4f%50%51%52%53%54%55%56%57%58%59%5a%30%31%32%33%34%35%36%37%38%39%60%2d%3d%5b%5d%5c%3b%27%2c%2e%2f%7e%21%40%23%24%25%5e%26%2a%28%29%5f%2b%7b%7d%7c%3a%22%3c%3e%3f';
        $out = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789`-=[]\\;\',./~!@#$%^&*()_+{}|:"<>?';
        $request = new Request();
        $request->setUri('http://example.com/' . $in);
        $route   = new Segment('/:foo');
        $match   = $route->match($request);

        $this->assertSame($out, $match->getParam('foo'));
    }

    public function testEncodeCache()
    {
        $params1 = ['p1' => 6.123, 'p2' => 7];
        $uri1 = 'example.com/'.implode('/', $params1);
        $params2 = ['p1' => 6, 'p2' => 'test'];
        $uri2 = 'example.com/'.implode('/', $params2);

        $route = new Segment('example.com/:p1/:p2');
        $request = new Request();

        $request->setUri($uri1);
        $route->match($request);
        $this->assertSame($uri1, $route->assemble($params1));

        $request->setUri($uri2);
        $route->match($request);
        $this->assertSame($uri2, $route->assemble($params2));
    }
}
