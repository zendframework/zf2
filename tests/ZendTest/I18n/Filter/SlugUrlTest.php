<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\Filter;

use Zend\I18n\Filter\SlugUrl as SlugUrlFilter;
use Locale;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class SlugUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
    
        $filter = new SlugUrlFilter();

        $text      = 'áàâéèêíìîóòôúùûã';
        $expected  = 'aaaeeeiiiooouuua';
        
        $this->assertEquals($expected, $filter->filter($text) );
    }

    public function testReplaceWhiteSpace()
    {
        
        $filter = new SlugUrlFilter(array(
            'replaceWhiteSpace' => '-' 
        ));

        $text = 'This is my nice text!';
        $expected = 'this-is-my-nice-text';
        
        $this->assertEquals($expected, $filter->filter($text) );
    }

    public function testOnlyAlNumCharacter()
    {
        
        $filter = new SlugUrlFilter(array(
            'onlyAlnum' => true
        ));

        $text = '! this is my / text $## ';
        $expected = '-this-is-my-text-';
       
        $this->assertEquals($expected, $filter->filter($text) );
         
    }


    public function testOnlyAlNumAndReplaceWhiteSpace()
    {

        $filter = new SlugUrlFilter(array(
            'replaceWhiteSpace' => '+'
        ));

        $text = '!this is my / text$##';
        $expected = 'this+is+my+text';

        $this->assertEquals($expected, $filter->filter($text) );
    }


    public function testIrrelevantCharsAndOnlyAlNum()
    {

        $filter = new SlugUrlFilter(array(
            'irrelevantChars' => '\/'
        ));

        $text = 'Welcome to Zend Framework/zf2 2';
        $expected = 'welcome-to-zend-framework-zf2-2';//replace backslash by a whitespace

        $this->assertEquals($expected, $filter->filter($text) );
    }

    public function testRelevantCharsAndOnlyAlNum()
    {
        $filter = new SlugUrlFilter();

        $text = 'The sum of a + a is equal to 2';

        $expected1 = 'the-sum-of-a-a-is-equal-to-2';
        $this->assertEquals($expected1, $filter->filter($text) );
        
        //removes the character space in place of irrelevant
        $filter->setIrrelevantChars('+');
        $expected2 = 'the-sum-of-a-a-is-equal-to-2';
        $this->assertEquals($expected2, $filter->filter($text) );
    }

    public function testRemoveGreekCharacters()
    {
        $filter = new SlugUrlFilter();
        $this->assertEquals('aaaabbbb', $filter->filter('φψξAAAAωϑBBBB') );
    }

}
