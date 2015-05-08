<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace ZendTest\Console\RouteMatcher;

use Zend\Console\RouteMatcher\DefaultRouteMatcher;

/**
 * @category   Zend
 * @package    Zend_Console
 * @subpackage UnitTests
 * @group      Zend_Console
 */
class DefaultRouteMatcherTest extends \PHPUnit_Framework_TestCase
{
    public static function routeProvider()
    {
        return [
            // -- mandatory long flags
            'mandatory-long-flag-no-match' => [
                '--foo --bar',
                ['a','b','--baz'],
                null
            ],
            'mandatory-long-flag-no-partial-match' => [
                '--foo --bar',
                ['--foo','--baz'],
                null
            ],
            'mandatory-long-flag-match' => [
                '--foo --bar',
                ['--foo','--bar'],
                ['foo' => true, 'bar' => true]
            ],
            'mandatory-long-flag-match-with-zero-value' => [
                '--foo=',
                ['--foo=0'],
                ['foo' => 0]
            ],
            'mandatory-long-flag-mixed-order-match' => [
                '--foo --bar',
                ['--bar','--foo'],
                ['foo' => true, 'bar' => true]
            ],
            'mandatory-long-flag-whitespace-in-definition' => [
                '      --foo   --bar ',
                ['--bar','--foo'],
                [
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                ]
            ],
            'mandatory-long-flag-alternative1' => [
                ' ( --foo | --bar )',
                ['--foo'],
                [
                    'foo' => true,
                    'bar' => false,
                    'baz' => null,
                ]
            ],
            'mandatory-long-flag-alternative2' => [
                ' ( --foo | --bar )',
                ['--bar'],
                [
                    'foo' => false,
                    'bar' => true,
                    'baz' => null,
                ]
            ],
            'mandatory-long-flag-alternative3' => [
                ' ( --foo | --bar )',
                ['--baz'],
                null
            ],

            // -- mandatory short flags
            'mandatory-short-flag-no-match' => [
                '-f -b',
                ['a','b','-f'],
                null
            ],
            'mandatory-short-flag-no-partial-match' => [
                '-f -b',
                ['-f','-z'],
                null
            ],
            'mandatory-short-flag-match' => [
                '-f -b',
                ['-f','-b'],
                ['f' => true, 'b' => true]
            ],
            'mandatory-short-flag-mixed-order-match' => [
                '-f -b',
                ['-b','-f'],
                ['f' => true, 'b' => true]
            ],
            'mandatory-short-flag-whitespace-in-definition' => [
                '      -f   -b ',
                ['-b','-f'],
                [
                    'f' => true,
                    'b' => true,
                    'baz' => null,
                ]
            ],
            'mandatory-short-flag-alternative1' => [
                ' ( -f | -b )',
                ['-f'],
                [
                    'f' => true,
                    'b' => false,
                    'baz' => null,
                ]
            ],
            'mandatory-short-flag-alternative2' => [
                ' ( -f | -b )',
                ['-b'],
                [
                    'f' => false,
                    'b' => true,
                    'baz' => null,
                ]
            ],
            'mandatory-short-flag-alternative3' => [
                ' ( -f | -b )',
                ['--baz'],
                null
            ],

            // -- optional long flags
            'optional-long-flag-non-existent' => [
                '--foo [--bar]',
                ['--foo'],
                [
                    'foo' => true,
                    'bar' => null,
                    'baz' => null,
                ]
            ],
            'literal-optional-long-flag' => [
                'foo [--bar]',
                ['foo', '--bar'],
                [
                    'foo' => null,
                    'bar' => true,
                ]
            ],
            'optional-long-flag-partial-mismatch' => [
                '--foo [--bar]',
                ['--foo', '--baz'],
                null
            ],
            'optional-long-flag-match' => [
                '--foo [--bar]',
                ['--foo','--bar'],
                [
                    'foo' => true,
                    'bar' => true
                ]
            ],
            'optional-long-value-flag-non-existent' => [
                '--foo [--bar=]',
                ['--foo'],
                [
                    'foo' => true,
                    'bar' => false
                ]
            ],
            'optional-long-flag-match-with-zero-value' => [
                '[--foo=]',
                ['--foo=0'],
                ['foo' => 0]
            ],
            'optional-long-value-flag' => [
                '--foo [--bar=]',
                ['--foo', '--bar=4'],
                [
                    'foo' => true,
                    'bar' => 4
                ]
            ],
            'optional-long-value-flag-non-existent-mixed-case' => [
                '--foo [--barBaz=]',
                ['--foo', '--barBaz=4'],
                [
                    'foo'    => true,
                    'barBaz' => 4
                ]
            ],
            'value-optional-long-value-flag' => [
                '<foo> [--bar=]',
                ['value', '--bar=4'],
                [
                    'foo' => 'value',
                    'bar' => 4
                ]
            ],
            'literal-optional-long-value-flag' => [
                'foo [--bar=]',
                ['foo', '--bar=4'],
                [
                    'foo' => null,
                    'bar' => 4,
                ]
            ],
            'optional-long-flag-mixed-order-match' => [
                '--foo --bar',
                ['--bar','--foo'],
                ['foo' => true, 'bar' => true]
            ],
            'optional-long-flag-whitespace-in-definition' => [
                '      --foo   [--bar] ',
                ['--bar','--foo'],
                [
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                ]
            ],
            'optional-long-flag-whitespace-in-definition2' => [
                '      --foo     [--bar      ] ',
                ['--bar','--foo'],
                [
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                ]
            ],
            'optional-long-flag-whitespace-in-definition3' => [
                '      --foo   [   --bar     ] ',
                ['--bar','--foo'],
                [
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                ]
            ],


            // -- value flags
            'mandatory-value-flag-syntax-1' => [
                '--foo=s',
                ['--foo','bar'],
                [
                    'foo' => 'bar',
                    'bar' => null
                ]
            ],
            'mandatory-value-flag-syntax-2' => [
                '--foo=',
                ['--foo','bar'],
                [
                    'foo' => 'bar',
                    'bar' => null
                ]
            ],
            'mandatory-value-flag-syntax-3' => [
                '--foo=anystring',
                ['--foo','bar'],
                [
                    'foo' => 'bar',
                    'bar' => null
                ]
            ],

            // -- edge cases for value flags values
            'mandatory-value-flag-equals-complex-1' => [
                '--foo=',
                ['--foo=SomeComplexValue=='],
                ['foo' => 'SomeComplexValue==']
            ],
            'mandatory-value-flag-equals-complex-2' => [
                '--foo=',
                ['--foo=...,</\/\\//""\'\'\'"\"'],
                ['foo' => '...,</\/\\//""\'\'\'"\"']
            ],
            'mandatory-value-flag-equals-complex-3' => [
                '--foo=',
                ['--foo====--'],
                ['foo' => '===--']
            ],
            'mandatory-value-flag-space-complex-1' => [
                '--foo=',
                ['--foo','SomeComplexValue=='],
                ['foo' => 'SomeComplexValue==']
            ],
            'mandatory-value-flag-space-complex-2' => [
                '--foo=',
                ['--foo','...,</\/\\//""\'\'\'"\"'],
                ['foo' => '...,</\/\\//""\'\'\'"\"']
            ],
            'mandatory-value-flag-space-complex-3' => [
                '--foo=',
                ['--foo','===--'],
                ['foo' => '===--']
            ],

            // -- required literal params
            'mandatory-literal-match-1' => [
                'foo',
                ['foo'],
                ['foo' => null]
            ],
            'mandatory-literal-match-2' => [
                'foo bar baz',
                ['foo','bar','baz'],
                ['foo' => null, 'bar' => null, 'baz' => null, 'bazinga' => null]
            ],
            'mandatory-literal-mismatch' => [
                'foo',
                ['fooo'],
                null
            ],
            'mandatory-literal-colon-match' => [
                'foo:bar',
                ['foo:bar'],
                ['foo:bar' => null]
            ],
            'mandatory-literal-colon-match-2' => [
                'foo:bar baz',
                ['foo:bar', 'baz'],
                ['foo:bar' => null, 'baz' => null]
            ],
            'mandatory-literal-order' => [
                'foo bar',
                ['bar','foo'],
                null
            ],
            'mandatory-literal-order-colon' => [
                'foo bar baz:inga',
                ['bar','foo', 'baz:inga'],
                null
            ],
            'mandatory-literal-partial-mismatch' => [
                'foo bar baz',
                ['foo','bar'],
                null
            ],
            'mandatory-literal-alternative-match-1' => [
                'foo ( bar | baz )',
                ['foo','bar'],
                ['foo' => null, 'bar' => true, 'baz' => false]
            ],
            'mandatory-literal-alternative-match-2' => [
                'foo (bar|baz)',
                ['foo','bar'],
                ['foo' => null, 'bar' => true, 'baz' => false]
            ],
            'mandatory-literal-alternative-match-3' => [
                'foo ( bar    |   baz )',
                ['foo','baz'],
                ['foo' => null, 'bar' => false, 'baz' => true]
            ],
            'mandatory-literal-alternative-mismatch' => [
                'foo ( bar |   baz )',
                ['foo','bazinga'],
                null
            ],
            'mandatory-literal-namedAlternative-match-1' => [
                'foo ( bar | baz ):altGroup',
                ['foo','bar'],
                ['foo' => null, 'altGroup'=>'bar', 'bar' => true, 'baz' => false]
            ],
            'mandatory-literal-namedAlternative-match-2' => [
                'foo ( bar |   baz   ):altGroup9',
                ['foo','baz'],
                ['foo' => null, 'altGroup9'=>'baz', 'bar' => false, 'baz' => true]
            ],
            'mandatory-literal-namedAlternative-mismatch' => [
                'foo ( bar |   baz   ):altGroup9',
                ['foo','bazinga'],
                null
            ],

            // -- optional literal params
            'optional-literal-match' => [
                'foo [bar] [baz]',
                ['foo','bar'],
                ['foo' => null, 'bar' => true, 'baz' => null]
            ],
            'optional-literal-colon-match' => [
                'foo [bar] [baz:inga]',
                ['foo','bar'],
                ['foo' => null, 'bar' => true, 'baz:inga' => null]
            ],
            'optional-literal-mismatch' => [
                'foo [bar] [baz]',
                ['baz','bar'],
                null
            ],
            'optional-literal-colon-mismatch' => [
                'foo [bar] [baz:inga]',
                ['baz:inga','bar'],
                null
            ],
            'optional-literal-shuffled-mismatch' => [
                'foo [bar] [baz]',
                ['foo','baz','bar'],
                null
            ],
            'optional-literal-alternative-match' => [
                'foo [bar | baz]',
                ['foo','baz'],
                ['foo' => null, 'baz' => true, 'bar' => false]
            ],
            'optional-literal-alternative-mismatch' => [
                'foo [bar | baz]',
                ['foo'],
                ['foo' => null, 'baz' => false, 'bar' => false]
            ],
            'optional-literal-namedAlternative-match-1' => [
                'foo [bar | baz]:altGroup1',
                ['foo','baz'],
                ['foo' => null, 'altGroup1' => 'baz', 'baz' => true, 'bar' => false]
            ],
            'optional-literal-namedAlternative-match-2' => [
                'foo [bar | baz | bazinga]:altGroup100',
                ['foo','bazinga'],
                ['foo' => null, 'altGroup100' => 'bazinga', 'bazinga' => true, 'baz' => false, 'bar' => false]
            ],
            'optional-literal-namedAlternative-match-3' => [
                'foo [ bar ]:altGroup100',
                ['foo','bar'],
                ['foo' => null, 'altGroup100' => 'bar', 'bar' => true, 'baz' => null]
            ],
            'optional-literal-namedAlternative-mismatch' => [
                'foo [ bar | baz ]:altGroup9',
                ['foo'],
                ['foo' => null, 'altGroup9'=> null, 'bar' => false, 'baz' => false]
            ],

            // -- value params
            'mandatory-value-param-syntax-1' => [
                'FOO',
                ['bar'],
                [
                    'foo' => 'bar',
                    'bar' => null
                ]
            ],
            'mandatory-value-param-syntax-2' => [
                '<foo>',
                ['bar'],
                [
                    'foo' => 'bar',
                    'bar' => null
                ]
            ],
            'mandatory-value-param-mixed-with-literal' => [
                'a b <foo> c',
                ['a','b','bar','c'],
                [
                    'a' => null,
                    'b' => null,
                    'foo' => 'bar',
                    'bar' => null,
                    'c' => null,
                ],
            ],
            'optional-value-param-1' => [
                'a b [<c>]',
                ['a','b','bar'],
                [
                    'a'   => null,
                    'b'   => null,
                    'c'   => 'bar',
                    'bar' => null,
                ],
            ],
            'optional-value-param-2' => [
                'a b [<c>]',
                ['a','b'],
                [
                    'a'   => null,
                    'b'   => null,
                    'c'   => null,
                    'bar' => null,
                ],
            ],
            'optional-value-param-3' => [
                'a b [<c>]',
                ['a','b','--c'],
                null
            ],

            // -- combinations
            'mandatory-long-short-alternative-1' => [
                ' ( --foo | -f )',
                ['--foo'],
                [
                    'foo' => true,
                    'f'   => false,
                    'baz' => null,
                ]
            ],
            'mandatory-long-short-alternative-2' => [
                ' ( --foo | -f )',
                ['-f'],
                [
                    'foo' => false,
                    'f'   => true,
                    'baz' => null,
                ]
            ],
            'optional-long-short-alternative-1' => [
                'a <b> [ --foo | -f ]',
                ['a','bar'],
                [
                    'a'   => null,
                    'b'   => 'bar',
                    'foo' => false,
                    'f'   => false,
                    'baz' => null,
                ]
            ],
            'optional-long-short-alternative-2' => [
                'a <b> [ --foo | -f ]',
                ['a','bar', '-f'],
                [
                    'a'   => null,
                    'b'   => 'bar',
                    'foo' => false,
                    'f'   => true,
                    'baz' => null,
                ]
            ],
            'optional-long-short-alternative-3' => [
                'a <b> [ --foo | -f ]',
                ['a','--foo', 'bar'],
                [
                    'a'   => null,
                    'b'   => 'bar',
                    'foo' => true,
                    'f'   => false,
                    'baz' => null,
                ]
            ],


            'mandatory-and-optional-value-params-with-flags-1' => [
                'a b <c> [<d>] [--eee|-e] [--fff|-f]',
                ['a','b','foo','bar'],
                [
                    'a'   => null,
                    'b'   => null,
                    'c'   => 'foo',
                    'd'   => 'bar',
                    'e'   => false,
                    'eee' => false,
                    'fff' => false,
                    'f'   => false,
                ],
            ],
            'mandatory-and-optional-value-params-with-flags-2' => [
                'a b <c> [<d>] [--eee|-e] [--fff|-f]',
                ['a','b','--eee', 'foo','bar'],
                [
                    'a'   => null,
                    'b'   => null,
                    'c'   => 'foo',
                    'd'   => 'bar',
                    'e'   => false,
                    'eee' => true,
                    'fff' => false,
                    'f'   => false,
                ],
            ],


            // -- overflows
            'too-many-arguments1' => [
                'foo bar',
                ['foo','bar','baz'],
                null
            ],
            'too-many-arguments2' => [
                'foo bar [baz]',
                ['foo','bar','baz','woo'],
                null,
            ],
            'too-many-arguments3' => [
                'foo bar [--baz]',
                ['foo','bar','--baz','woo'],
                null,
            ],
            'too-many-arguments4' => [
                'foo bar [--baz] woo',
                ['foo','bar','woo'],
                [
                    'foo' => null,
                    'bar' => null,
                    'baz' => false,
                    'woo' => null
                ]
            ],
            'too-many-arguments5' => [
                '--foo --bar [--baz] woo',
                ['--bar','--foo','woo'],
                [
                    'foo' => true,
                    'bar' => true,
                    'baz' => false,
                    'woo' => null
                ]
            ],
            'too-many-arguments6' => [
                '--foo --bar [--baz]',
                ['--bar','--foo','woo'],
                null
            ],

            // other (combination)
            'combined-1' => [
                'literal <bar> [--foo=] --baz',
                ['literal', 'oneBar', '--foo=4', '--baz'],
                [
                    'literal' => null,
                    'bar' => 'oneBar',
                    'foo' => 4,
                    'baz' => true
                ]
            ],
            // group with group name diferent than options (short)
            'group-1' => [
                'group [-t|--test]:testgroup',
                ['group', '-t'],
                [
                    'group' => null,
                    'testgroup' => true,
                ]
            ],
            // group with group name diferent than options (long)
            'group-2' => [
                'group [-t|--test]:testgroup',
                ['group', '--test'],
                [
                    'group' => null,
                    'testgroup' => true,
                ]
            ],
            // group with same name as option (short)
            'group-3' => [
                'group [-t|--test]:test',
                ['group', '-t'],
                [
                    'group' => null,
                    'test' => true,
                ]
            ],
            // group with same name as option (long)
            'group-4' => [
                'group [-t|--test]:test',
                ['group', '--test'],
                [
                    'group' => null,
                    'test' => true,
                ]
            ],
            'group-5' => [
                'group (-t | --test ):test',
                ['group', '--test'],
                [
                    'group' => null,
                    'test' => true,
                ],
            ],
            'group-6' => [
                'group (-t | --test ):test',
                ['group', '-t'],
                [
                    'group' => null,
                    'test' => true,
                ],
            ],
            'group-7' => [
                'group [-x|-y|-z]:test',
                ['group', '-y'],
                [
                    'group' => null,
                    'test' => true,
                ],
            ],
            'group-8' => [
                'group [--foo|--bar|--baz]:test',
                ['group', '--foo'],
                [
                    'group' => null,
                    'test' => true,
                ],
            ],
            'group-9' => [
                'group (--foo|--bar|--baz):test',
                ['group', '--foo'],
                [
                    'group' => null,
                    'test' => true,
                ],
            ],

            /**
             * @bug ZF2-4315
             * @link https://github.com/zendframework/zf2/issues/4315
             */
            'literal-with-dashes' => [
                'foo-bar-baz [--bar=]',
                ['foo-bar-baz',],
                [
                    'foo-bar-baz' => null,
                    'foo'         => null,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],

            'literal-optional-with-dashes' => [
                '[foo-bar-baz] [--bar=]',
                ['foo-bar-baz'],
                [
                    'foo-bar-baz' => true,
                    'foo'         => null,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'literal-optional-with-dashes2' => [
                'foo [foo-bar-baz] [--bar=]',
                ['foo'],
                [
                    'foo-bar-baz' => false,
                    'foo'         => null,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'literal-alternative-with-dashes' => [
                '(foo-bar|foo-baz) [--bar=]',
                ['foo-bar',],
                [
                    'foo-bar'     => true,
                    'foo-baz'     => false,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'literal-optional-alternative-with-dashes' => [
                '[foo-bar|foo-baz] [--bar=]',
                ['foo-baz',],
                [
                    'foo-bar'     => false,
                    'foo-baz'     => true,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'literal-optional-alternative-with-dashes2' => [
                'foo [foo-bar|foo-baz] [--bar=]',
                ['foo',],
                [
                    'foo'         => null,
                    'foo-bar'     => false,
                    'foo-baz'     => false,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'literal-flag-with-dashes' => [
                'foo --bar-baz',
                ['foo','--bar-baz'],
                [
                    'foo'         => null,
                    'bar-baz'     => true,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'literal-optional-flag-with-dashes' => [
                'foo [--bar-baz]',
                ['foo','--bar-baz'],
                [
                    'foo'         => null,
                    'bar-baz'     => true,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'literal-optional-flag-with-dashes2' => [
                'foo [--bar-baz]',
                ['foo'],
                [
                    'foo'         => null,
                    'bar-baz'     => false,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'literal-optional-flag-alternative-with-dashes' => [
                'foo [--foo-bar|--foo-baz]',
                ['foo','--foo-baz'],
                [
                    'foo'         => null,
                    'foo-bar'     => false,
                    'foo-baz'     => true,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'literal-optional-flag-alternative-with-dashes2' => [
                'foo [--foo-bar|--foo-baz]',
                ['foo'],
                [
                    'foo'         => null,
                    'foo-bar'     => false,
                    'foo-baz'     => false,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'value-with-dashes' => [
                '<foo-bar-baz> [--bar=]',
                ['abc',],
                [
                    'foo-bar-baz' => 'abc',
                    'foo'         => null,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],

            'value-optional-with-dashes' => [
                '[<foo-bar-baz>] [--bar=]',
                ['abc'],
                [
                    'foo-bar-baz' => 'abc',
                    'foo'         => null,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'value-optional-with-dashes2' => [
                '[<foo-bar-baz>] [--bar=]',
                ['--bar','abc'],
                [
                    'foo-bar-baz' => null,
                    'foo'         => null,
                    'bar'         => 'abc',
                    'baz'         => null,
                    'something'   => null,
                ]
            ],
            'value-optional-with-mixed-case' => [
                '[<mixedCaseParam>] [--bar=]',
                ['aBc', '--bar','abc'],
                [
                    'mixedCaseParam' => 'aBc',
                    'foo'            => null,
                    'bar'            => 'abc',
                    'baz'            => null,
                    'something'      => null,
                ]
            ],
            'value-optional-with-upper-case' => [
                '[<UPPERCASEPARAM>] [--bar=]',
                ['aBc', '--bar', 'abc'],
                [
                    'UPPERCASEPARAM' => 'aBc',
                    'foo'            => null,
                    'bar'            => 'abc',
                    'baz'            => null,
                    'something'      => null,
                ]
            ],
            /**
             * @bug ZF2-5671
             * @link https://github.com/zendframework/zf2/issues/5671
             */
            'mandatory-literal-camel-case' => [
                'FooBar',
                ['FooBar'],
                ['FooBar' => null],
            ],
            'mandatory-literal-camel-case-no-match' => [
                'FooBar',
                ['foobar'],
                null,
            ],
            'optional-literal-camel-case' => [
                '[FooBar]',
                ['FooBar'],
                ['FooBar' => true],
            ],
            'optional-literal-camel-case-no-match' => [
                '[FooBar]',
                ['foobar'],
                null,
            ],
            'optional-literal-alternative-camel-case' => [
                '[ FooBar | FoozBar ]',
                ['FooBar'],
                ['FooBar' => true],
            ],
            'mandatory-literal-alternative-camel-case' => [
                '( FooBar | FoozBar )',
                ['FooBar'],
                ['FooBar' => true],
            ],
            'mandatory-literal-alternative-camel-case-no-match' => [
                '( FooBar | FoozBar )',
                ['baz'],
                null,
            ],
        ];
    }

    /**
     * @dataProvider routeProvider
     * @param        string         $routeDefinition
     * @param        array          $arguments
     * @param        array|null     $params
     */
    public function testMatching($routeDefinition, array $arguments = [], array $params = null)
    {
        $route = new DefaultRouteMatcher($routeDefinition);
        $match = $route->match($arguments);


        if ($params === null) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInternalType('array', $match);

            foreach ($params as $key => $value) {
                $this->assertEquals(
                    $value,
                    isset($match[$key])?$match[$key]:null,
                    $value === null ? "Param $key is not present" : "Param $key is present and is equal to $value"
                );
            }
        }
    }

    public function testCannotMatchWithEmptyMandatoryParam()
    {
        $arguments = ['--foo='];
        $route = new DefaultRouteMatcher('--foo=');
        $match = $route->match($arguments);
        $this->assertEquals(null, $match);
    }

    public static function routeDefaultsProvider()
    {
        return [
            'required-literals-no-defaults' => [
                'create controller',
                [],
                ['create', 'controller'],
                ['create' => null, 'controller' => null],
            ],
            'required-literals-defaults' => [
                'create controller',
                ['controller' => 'value'],
                ['create', 'controller'],
                ['create' => null, 'controller' => 'value'],
            ],
            'value-param-no-defaults' => [
                'create controller <controller>',
                [],
                ['create', 'controller', 'foo'],
                ['create' => null, 'controller' => 'foo'],
            ],
            'value-param-defaults-overridden' => [
                'create controller <controller>',
                ['controller' => 'defaultValue'],
                ['create', 'controller', 'foo'],
                ['create' => null, 'controller' => 'foo'],
            ],
            'optional-value-param-defaults' => [
                'create controller [<controller>]',
                ['controller' => 'defaultValue'],
                ['create', 'controller'],
                ['create' => null, 'controller' => 'defaultValue'],
            ],
            'alternative-literal-non-present' => [
                '(foo | bar)',
                ['bar' => 'something'],
                ['foo'],
                ['foo' => true, 'bar' => false],
            ],
            'alternative-literal-present' => [
                '(foo | bar)',
                ['bar' => 'something'],
                ['bar'],
                ['foo' => false, 'bar' => 'something'],
            ],
            'alternative-flag-non-present' => [
                '(--foo | --bar)',
                ['bar' => 'something'],
                ['--foo'],
                ['foo' => true, 'bar' => false],
            ],
            'alternative-flag-present' => [
                '(--foo | --bar)',
                ['bar' => 'something'],
                ['--bar'],
                ['foo' => false, 'bar' => 'something'],
            ],
            'optional-literal-non-present' => [
                'foo [bar]',
                ['bar' => 'something'],
                ['foo'],
                ['foo' => null, 'bar' => false],
            ],
            'optional-literal-present' => [
                'foo [bar]',
                ['bar' => 'something'],
                ['foo', 'bar'],
                ['foo' => null, 'bar' => 'something'],
            ],
        ];
    }

    /**
     * @dataProvider routeDefaultsProvider
     * @param        string         $routeDefinition
     * @param        array          $defaults
     * @param        array          $arguments
     * @param        array|null     $params
     */
    public function testMatchingWithDefaults(
        $routeDefinition,
        array $defaults = [],
        array $arguments = [],
        array $params = null
    ) {
        $route = new DefaultRouteMatcher($routeDefinition, [], $defaults);
        $match = $route->match($arguments);

        if ($params === null) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInternalType('array', $match);

            foreach ($params as $key => $value) {
                $this->assertSame(
                    $value,
                    isset($match[$key])?$match[$key]:null,
                    $value === null ? "Param $key is not present" : "Param $key is present and is equal to '$value'"
                );
            }
        }
    }

    public static function routeConstraintsProvider()
    {
        return [
            'simple-constraints' => [
                '<numeric> <alpha>',
                [
                    'numeric' => '/^[0-9]+$/',
                    'alpha'   => '/^[a-zA-Z]+$/',
                ],
                ['1234', 'test'],
                true
            ],
            'constraints-on-optional-param' => [
                '<alpha> [<numeric>]',
                [
                    'numeric' => '/^[0-9]+$/',
                    'alpha'   => '/^[a-zA-Z]+$/',
                ],
                ['test', '1234'],
                true
            ],
            'optional-empty-param' => [
                '<alpha> [<numeric>]',
                [
                    'numeric' => '/^[0-9]+$/',
                    'alpha'   => '/^[a-zA-Z]+$/',
                ],
                ['test'],
                true
            ],
            'named-param' => [
                '--foo=',
                [
                    'foo' => '/^bar$/'
                ],
                ['--foo=bar'],
                true,
            ],
            'failing-param' => [
                '<good1> <good2> <bad>',
                [
                    'good1'   => '/^[a-zA-Z]+$/',
                    'good2'   => '/^[a-zA-Z]+$/',
                    'bad'   => '/^[a-zA-Z]+$/',
                ],
                ['foo', 'bar', 'foo123bar'],
                false
            ],
            'failing-optional-param' => [
                '<good> [<bad>]',
                [
                    'good2'   => '/^(foo|bar)$/',
                    'bad'   => '/^(foo|bar)$/',
                ],
                ['foo', 'baz'],
                false
            ],
            'failing-named-param' => [
                '--foo=',
                [
                    'foo' => '/^bar$/'
                ],
                ['--foo=baz'],
                false,
            ],
        ];
    }

    /**
     * @dataProvider routeConstraintsProvider
     * @param        string $routeDefinition
     * @param        array  $constraints
     * @param        array  $arguments
     * @param        bool   $shouldMatch
     */
    public function testMatchingWithConstraints(
        $routeDefinition,
        array $constraints = [],
        array $arguments = [],
        $shouldMatch = true
    ) {
        $route = new DefaultRouteMatcher($routeDefinition, $constraints);
        $match = $route->match($arguments);

        if ($shouldMatch === false) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInternalType('array', $match);
        }
    }

    public static function routeAliasesProvider()
    {
        return [
            'simple-alias' => [
                '--user=',
                [
                    'username' => 'user'
                ],
                ['--username=JohnDoe'],
                [
                    'user' => 'JohnDoe'
                ]
            ],
            'multiple-aliases' => [
                '--name= --email=',
                [
                    'username' => 'name',
                    'useremail' => 'email'
                ],
                ['--username=JohnDoe', '--useremail=johndoe@domain.com'],
                [
                    'name' => 'JohnDoe',
                    'email' => 'johndoe@domain.com',
                ]
            ],
            'flags' => [
                'foo --bar',
                [
                    'baz' => 'bar'
                ],
                ['foo', '--baz'],
                [
                    'bar' => true
                ]
            ],
            'with-alternatives' => [
                'do-something (--remove|--update)',
                [
                    'delete' => 'remove'
                ],
                ['do-something', '--delete'],
                [
                    'remove' => true,
                ]
            ],
            'with-alternatives-2' => [
                'do-something (--update|--remove)',
                [
                    'delete' => 'remove'
                ],
                ['do-something', '--delete'],
                [
                    'remove' => true,
                ]
            ]
        ];
    }

    /**
     * @dataProvider routeAliasesProvider
     * @param        string     $routeDefinition
     * @param        array      $aliases
     * @param        array      $arguments
     * @param        array|null $params
     */
    public function testMatchingWithAliases(
        $routeDefinition,
        array $aliases = [],
        array $arguments = [],
        array $params = null
    ) {
        $route = new DefaultRouteMatcher($routeDefinition, [], [], $aliases);
        $match = $route->match($arguments);

        if ($params === null) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInternalType('array', $match);

            foreach ($params as $key => $value) {
                $this->assertEquals(
                    $value,
                    isset($match[$key])?$match[$key]:null,
                    $value === null ? "Param $key is not present" : "Param $key is present and is equal to $value"
                );
            }
        }
    }

    public function routeValidatorsProvider()
    {
        return [
            'validators-valid' => [
                '<string> <number>',
                [
                    'string' => new \Zend\Validator\StringLength(['min' => 5, 'max' => 12]),
                    'number' => new \Zend\Validator\Digits()
                ],
                ['foobar', '12345'],
                true
            ],
            'validators-invalid' => [
                '<string> <number>',
                [
                    'string' => new \Zend\Validator\StringLength(['min' => 5, 'max' => 12]),
                    'number' => new \Zend\Validator\Digits()
                ],
                ['foo', '12345'],
                false
            ],
            'validators-invalid2' => [
                '<number> <string>',
                [
                    'string' => new \Zend\Validator\StringLength(['min' => 5, 'max' => 12]),
                    'number' => new \Zend\Validator\Digits()
                ],
                ['foozbar', 'not_digits'],
                false
            ],

        ];
    }

    /**
     * @dataProvider routeValidatorsProvider
     * @param string $routeDefinition
     * @param array $validators
     * @param array $arguments
     * @param bool $shouldMatch
     */
    public function testParamsCanBeValidated($routeDefinition, $validators, $arguments, $shouldMatch)
    {
        $matcher = new DefaultRouteMatcher($routeDefinition, [], [], [], null, $validators);
        $match = $matcher->match($arguments);
        if ($shouldMatch === false) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInternalType('array', $match);
        }
    }

    public function routeFiltersProvider()
    {
        $genericFilter = $this->getMock('Zend\Filter\FilterInterface', ['filter']);
        $genericFilter->expects($this->once())->method('filter')
            ->with('foobar')->will($this->returnValue('foobaz'));

        return [
            'filters-generic' => [
                '<param>',
                [
                    'param' => $genericFilter
                ],
                ['foobar'],
                [
                    'param' => 'foobaz'
                ]
            ],
            'filters-single' => [
                '<number>',
                [
                    'number' => new \Zend\Filter\ToInt()
                ],
                ['123four'],
                [
                    'number' => 123
                ]
            ],
            'filters-multiple' => [
                '<number> <strtolower>',
                [
                    'number' => new \Zend\Filter\ToInt(),
                    'strtolower' => new \Zend\Filter\StringToLower(),
                ],
                ['nan', 'FOOBAR'],
                [
                    'number' => 0,
                    'strtolower' => 'foobar'
                ]
            ],
        ];
    }

    /**
     * @dataProvider routeFiltersProvider
     * @param string $routeDefinition
     * @param array $filters
     * @param array $arguments
     * @param array $params
     */
    public function testParamsCanBeFiltered($routeDefinition, $filters, $arguments, $params)
    {
        $matcher = new DefaultRouteMatcher($routeDefinition, [], [], [], $filters);
        $match = $matcher->match($arguments);

        if (null === $match) {
            $this->fail("Route '$routeDefinition' must match.'");
        }

        $this->assertInternalType('array', $match);

        foreach ($params as $key => $value) {
            $this->assertEquals(
                $value,
                isset($match[$key])?$match[$key]:null,
                $value === null ? "Param $key is not present" : "Param $key is present and is equal to $value"
            );
        }
    }

    public function testConstructorDoesNotAcceptInvalidFilters()
    {
        $this->setExpectedException('Zend\Console\Exception\InvalidArgumentException');
        new DefaultRouteMatcher('<foo>', [], [], [], [
            new \stdClass()
        ]);
    }

    public function testConstructorDoesNotAcceptInvalidValidators()
    {
        $this->setExpectedException('Zend\Console\Exception\InvalidArgumentException');
        new DefaultRouteMatcher('<foo>', [], [], [], [], [
            new \stdClass()
        ]);
    }
}
