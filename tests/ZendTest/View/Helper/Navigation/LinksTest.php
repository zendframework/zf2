<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper\Navigation;

use Zend\Config;
use Zend\Navigation\Page\AbstractPage;
use Zend\Navigation\Page\Uri as UriPage;
use Zend\Permissions\Acl;
use Zend\Permissions\Acl\Role;
use Zend\Permissions\Acl\Resource;
use Zend\View;
use Zend\View\Helper\Doctype;
use Zend\View\Helper\Navigation;

class LinksTest extends AbstractTest
{
    /**
     * View helper
     *
     * @var Navigation\Links
     */
    protected $helper;

    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $helperName = 'Zend\View\Helper\Navigation\Links';

    /**
     * @var Doctype
     */
    protected $helperDoctype;

    /**
     * @var Doctype
     */
    private $helperDoctypeOld;

    public function setUp()
    {
        parent::setUp();

        // doctype fix (someone forgot to clean up after their unit tests)
        $this->helperDoctype = $this->helper->getView()->plugin('doctype');
        $this->helperDoctypeOld = $this->helperDoctype->getDoctype();
        $this->helperDoctype->setDoctype(Doctype::HTML4_LOOSE);

        // disable all active pages
        foreach ($this->helper->findAllByActive(true) as $page) {
            $page->active = false;
        }
    }

    public function tearDown()
    {
        return;
        $this->helperDoctype->setDoctype($this->helperDoctypeOld);
    }

    public function testCanRenderFromServiceAlias()
    {
        $sm = $this->serviceManager;
        $this->helper->setServiceLocator($sm);

        $returned = $this->helper->render('Navigation');
        $this->assertEquals($returned, $this->_getExpected('links/default.html'));
    }

    public function testHelperEntryPointWithoutAnyParams()
    {
        $returned = $this->helper->__invoke();
        $this->assertEquals($this->helper, $returned);
        $this->assertEquals($this->_nav1, $returned->getContainer());
    }

    public function testHelperEntryPointWithContainerParam()
    {
        $returned = $this->helper->__invoke($this->_nav2);
        $this->assertEquals($this->helper, $returned);
        $this->assertEquals($this->_nav2, $returned->getContainer());
    }

    public function testDoNotRenderIfNoPageIsActive()
    {
        $this->assertEquals('', $this->helper->render());
    }

    public function testDetectRelationFromStringPropertyOfActivePage()
    {
        $active = $this->helper->findOneByLabel('Page 2');
        $active->addRel('example', 'http://www.example.com/');
        $found = $this->helper->findRelation($active, 'rel', 'example');

        $expected = array(
            'type'  => 'Zend\Navigation\Page\Uri',
            'href'  => 'http://www.example.com/',
            'label' => null
        );

        $actual = array(
            'type'  => get_class($found),
            'href'  => $found->getHref(),
            'label' => $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectRelationFromPageInstancePropertyOfActivePage()
    {
        $active = $this->helper->findOneByLabel('Page 2');
        $active->addRel('example', AbstractPage::factory(array(
            'uri' => 'http://www.example.com/',
            'label' => 'An example page'
        )));
        $found = $this->helper->findRelExample($active);

        $expected = array(
            'type'  => 'Zend\Navigation\Page\Uri',
            'href'  => 'http://www.example.com/',
            'label' => 'An example page'
        );

        $actual = array(
            'type'  => get_class($found),
            'href'  => $found->getHref(),
            'label' => $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectRelationFromArrayPropertyOfActivePage()
    {
        $active = $this->helper->findOneByLabel('Page 2');
        $active->addRel('example', array(
            'uri' => 'http://www.example.com/',
            'label' => 'An example page'
        ));
        $found = $this->helper->findRelExample($active);

        $expected = array(
            'type'  => 'Zend\Navigation\Page\Uri',
            'href'  => 'http://www.example.com/',
            'label' => 'An example page'
        );

        $actual = array(
            'type'  => get_class($found),
            'href'  => $found->getHref(),
            'label' => $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectRelationFromConfigInstancePropertyOfActivePage()
    {
        $active = $this->helper->findOneByLabel('Page 2');
        $active->addRel('example', new Config\Config(array(
            'uri' => 'http://www.example.com/',
            'label' => 'An example page'
        )));
        $found = $this->helper->findRelExample($active);

        $expected = array(
            'type'  => 'Zend\Navigation\Page\Uri',
            'href'  => 'http://www.example.com/',
            'label' => 'An example page'
        );

        $actual = array(
            'type'  => get_class($found),
            'href'  => $found->getHref(),
            'label' => $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectMultipleRelationsFromArrayPropertyOfActivePage()
    {
        $active = $this->helper->findOneByLabel('Page 2');

        $active->addRel('alternate', array(
            array(
                'label' => 'foo',
                'uri'   => 'bar'
            ),
            array(
                'label' => 'baz',
                'uri'   => 'bat'
            )
        ));

        $found = $this->helper->findRelAlternate($active);

        $expected = array('type' => 'array', 'count' => 2);
        $actual = array('type' => gettype($found), 'count' => count($found));
        $this->assertEquals($expected, $actual);
    }

    public function testDetectMultipleRelationsFromConfigPropertyOfActivePage()
    {
        $active = $this->helper->findOneByLabel('Page 2');

        $active->addRel('alternate', new Config\Config(array(
            array(
                'label' => 'foo',
                'uri'   => 'bar'
            ),
            array(
                'label' => 'baz',
                'uri'   => 'bat'
            )
        )));

        $found = $this->helper->findRelAlternate($active);

        $expected = array('type' => 'array', 'count' => 2);
        $actual = array('type' => gettype($found), 'count' => count($found));
        $this->assertEquals($expected, $actual);
    }

    public function testExtractingRelationsFromPageProperties()
    {
        $types = array(
            'alternate', 'stylesheet', 'start', 'next', 'prev', 'contents',
            'index', 'glossary', 'copyright', 'chapter', 'section', 'subsection',
            'appendix', 'help', 'bookmark'
        );

        $samplePage = AbstractPage::factory(array(
            'label' => 'An example page',
            'uri'   => 'http://www.example.com/'
        ));

        $active = $this->helper->findOneByLabel('Page 2');
        $expected = array();
        $actual = array();

        foreach ($types as $type) {
            $active->addRel($type, $samplePage);
            $found = $this->helper->findRelation($active, 'rel', $type);

            $expected[$type] = $samplePage->getLabel();
            $actual[$type]   = $found->getLabel();

            $active->removeRel($type);
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindStartPageByTraversal()
    {
        $active = $this->helper->findOneByLabel('Page 2.1');
        $expected = 'Home';
        $actual = $this->helper->findRelStart($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testDoNotFindStartWhenGivenPageIsTheFirstPage()
    {
        $active = $this->helper->findOneByLabel('Home');
        $actual = $this->helper->findRelStart($active);
        $this->assertNull($actual, 'Should not find any start page');
    }

    public function testFindNextPageByTraversalShouldFindChildPage()
    {
        $active = $this->helper->findOneByLabel('Page 2');
        $expected = 'Page 2.1';
        $actual = $this->helper->findRelNext($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindNextPageByTraversalShouldFindSiblingPage()
    {
        $active = $this->helper->findOneByLabel('Page 2.1');
        $expected = 'Page 2.2';
        $actual = $this->helper->findRelNext($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindNextPageByTraversalShouldWrap()
    {
        $active = $this->helper->findOneByLabel('Page 2.2.2');
        $expected = 'Page 2.3';
        $actual = $this->helper->findRelNext($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindPrevPageByTraversalShouldFindParentPage()
    {
        $active = $this->helper->findOneByLabel('Page 2.1');
        $expected = 'Page 2';
        $actual = $this->helper->findRelPrev($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindPrevPageByTraversalShouldFindSiblingPage()
    {
        $active = $this->helper->findOneByLabel('Page 2.2');
        $expected = 'Page 2.1';
        $actual = $this->helper->findRelPrev($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindPrevPageByTraversalShouldWrap()
    {
        $active = $this->helper->findOneByLabel('Page 2.3');
        $expected = 'Page 2.2.2';
        $actual = $this->helper->findRelPrev($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testShouldFindChaptersFromFirstLevelOfPagesInContainer()
    {
        $active = $this->helper->findOneByLabel('Page 2.3');
        $found = $this->helper->findRelChapter($active);

        $expected = array('Page 1', 'Page 2', 'Page 3', 'Zym');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindingChaptersShouldExcludeSelfIfChapter()
    {
        $active = $this->helper->findOneByLabel('Page 2');
        $found = $this->helper->findRelChapter($active);

        $expected = array('Page 1', 'Page 3', 'Zym');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindSectionsWhenActiveChapterPage()
    {
        $active = $this->helper->findOneByLabel('Page 2');
        $found = $this->helper->findRelSection($active);
        $expected = array('Page 2.1', 'Page 2.2', 'Page 2.3');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testDoNotFindSectionsWhenActivePageIsASection()
    {
        $active = $this->helper->findOneByLabel('Page 2.2');
        $found = $this->helper->findRelSection($active);
        $this->assertNull($found);
    }

    public function testDoNotFindSectionsWhenActivePageIsASubsection()
    {
        $active = $this->helper->findOneByLabel('Page 2.2.1');
        $found = $this->helper->findRelation($active, 'rel', 'section');
        $this->assertNull($found);
    }

    public function testFindSubsectionWhenActivePageIsSection()
    {
        $active = $this->helper->findOneByLabel('Page 2.2');
        $found = $this->helper->findRelSubsection($active);

        $expected = array('Page 2.2.1', 'Page 2.2.2');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testDoNotFindSubsectionsWhenActivePageIsASubSubsection()
    {
        $active = $this->helper->findOneByLabel('Page 2.2.1');
        $found = $this->helper->findRelSubsection($active);
        $this->assertNull($found);
    }

    public function testDoNotFindSubsectionsWhenActivePageIsAChapter()
    {
        $active = $this->helper->findOneByLabel('Page 2');
        $found = $this->helper->findRelSubsection($active);
        $this->assertNull($found);
    }

    public function testFindRevSectionWhenPageIsSection()
    {
        $active = $this->helper->findOneByLabel('Page 2.2');
        $found = $this->helper->findRevSection($active);
        $this->assertEquals('Page 2', $found->getLabel());
    }

    public function testFindRevSubsectionWhenPageIsSubsection()
    {
        $active = $this->helper->findOneByLabel('Page 2.2.1');
        $found = $this->helper->findRevSubsection($active);
        $this->assertEquals('Page 2.2', $found->getLabel());
    }

    public function testAclFiltersAwayPagesFromPageProperty()
    {
        $acl = new Acl\Acl();
        $acl->addRole(new Role\GenericRole('member'));
        $acl->addRole(new Role\GenericRole('admin'));
        $acl->addResource(new Resource\GenericResource('protected'));
        $acl->allow('admin', 'protected');
        $this->helper->setAcl($acl);
        $this->helper->setRole($acl->getRole('member'));

        $samplePage = AbstractPage::factory(array(
            'label'    => 'An example page',
            'uri'      => 'http://www.example.com/',
            'resource' => 'protected'
        ));

        $active = $this->helper->findOneByLabel('Home');
        $expected = array(
            'alternate'  => false,
            'stylesheet' => false,
            'start'      => false,
            'next'       => 'Page 1',
            'prev'       => false,
            'contents'   => false,
            'index'      => false,
            'glossary'   => false,
            'copyright'  => false,
            'chapter'    => 'array(4)',
            'section'    => false,
            'subsection' => false,
            'appendix'   => false,
            'help'       => false,
            'bookmark'   => false
        );
        $actual = array();

        foreach ($expected as $type => $discard) {
            $active->addRel($type, $samplePage);

            $found = $this->helper->findRelation($active, 'rel', $type);
            if (null === $found) {
                $actual[$type] = false;
            } elseif (is_array($found)) {
                $actual[$type] = 'array(' . count($found) . ')';
            } else {
                $actual[$type] = $found->getLabel();
            }
        }

        $this->assertEquals($expected, $actual);
    }

    public function testAclFiltersAwayPagesFromContainerSearch()
    {
        $acl = new Acl\Acl();
        $acl->addRole(new Role\GenericRole('member'));
        $acl->addRole(new Role\GenericRole('admin'));
        $acl->addResource(new Resource\GenericResource('protected'));
        $acl->allow('admin', 'protected');
        $this->helper->setAcl($acl);
        $this->helper->setRole($acl->getRole('member'));

        $oldContainer = $this->helper->getContainer();
        $container = $this->helper->getContainer();
        $iterator = new \RecursiveIteratorIterator(
            $container,
            \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $page) {
            $page->resource = 'protected';
        }
        $this->helper->setContainer($container);

        $active = $this->helper->findOneByLabel('Home');
        $search = array(
            'start'      => 'Page 1',
            'next'       => 'Page 1',
            'prev'       => 'Page 1.1',
            'chapter'    => 'Home',
            'section'    => 'Page 1',
            'subsection' => 'Page 2.2'
        );

        $expected = array();
        $actual = array();

        foreach ($search as $type => $active) {
            $expected[$type] = false;

            $active = $this->helper->findOneByLabel($active);
            $found = $this->helper->findRelation($active, 'rel', $type);

            if (null === $found) {
                $actual[$type] = false;
            } elseif (is_array($found)) {
                $actual[$type] = 'array(' . count($found) . ')';
            } else {
                $actual[$type] = $found->getLabel();
            }
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindRelationMustSpecifyRelOrRev()
    {
        $active = $this->helper->findOneByLabel('Home');
        try {
            $this->helper->findRelation($active, 'foo', 'bar');
            $this->fail('An invalid value was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (View\Exception\ExceptionInterface $e) {
            $this->assertContains('Invalid argument: $rel', $e->getMessage());
        }
    }

    public function testRenderLinkMustSpecifyRelOrRev()
    {
        $active = $this->helper->findOneByLabel('Home');
        try {
            $this->helper->renderLink($active, 'foo', 'bar');
            $this->fail('An invalid value was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (View\Exception\ExceptionInterface $e) {
            $this->assertContains('Invalid relation attribute', $e->getMessage());
        }
    }

    public function testFindAllRelations()
    {
        $expectedRelations = array(
            'alternate'  => array('Forced page'),
            'stylesheet' => array('Forced page'),
            'start'      => array('Forced page'),
            'next'       => array('Forced page'),
            'prev'       => array('Forced page'),
            'contents'   => array('Forced page'),
            'index'      => array('Forced page'),
            'glossary'   => array('Forced page'),
            'copyright'  => array('Forced page'),
            'chapter'    => array('Forced page'),
            'section'    => array('Forced page'),
            'subsection' => array('Forced page'),
            'appendix'   => array('Forced page'),
            'help'       => array('Forced page'),
            'bookmark'   => array('Forced page'),
            'canonical'  => array('Forced page'),
            'home'       => array('Forced page')
        );

        // build expected result
        $expected = array(
            'rel' => $expectedRelations,
            'rev' => $expectedRelations
        );

        // find active page and create page to use for relations
        $active = $this->helper->findOneByLabel('Page 1');
        $forcedRelation = new UriPage(array(
            'label' => 'Forced page',
            'uri'   => '#'
        ));

        // add relations to active page
        foreach ($expectedRelations as $type => $discard) {
            $active->addRel($type, $forcedRelation);
            $active->addRev($type, $forcedRelation);
        }

        // build actual result
        $actual = $this->helper->findAllRelations($active);
        foreach ($actual as $attrib => $relations) {
            foreach ($relations as $type => $pages) {
                foreach ($pages as $key => $page) {
                    $actual[$attrib][$type][$key] = $page->getLabel();
                }
            }
        }

        $this->assertEquals($expected, $actual);
    }

    private function _getFlags()
    {
        return array(
            Navigation\Links::RENDER_ALTERNATE  => 'alternate',
            Navigation\Links::RENDER_STYLESHEET => 'stylesheet',
            Navigation\Links::RENDER_START      => 'start',
            Navigation\Links::RENDER_NEXT       => 'next',
            Navigation\Links::RENDER_PREV       => 'prev',
            Navigation\Links::RENDER_CONTENTS   => 'contents',
            Navigation\Links::RENDER_INDEX      => 'index',
            Navigation\Links::RENDER_GLOSSARY   => 'glossary',
            Navigation\Links::RENDER_CHAPTER    => 'chapter',
            Navigation\Links::RENDER_SECTION    => 'section',
            Navigation\Links::RENDER_SUBSECTION => 'subsection',
            Navigation\Links::RENDER_APPENDIX   => 'appendix',
            Navigation\Links::RENDER_HELP       => 'help',
            Navigation\Links::RENDER_BOOKMARK   => 'bookmark',
            Navigation\Links::RENDER_CUSTOM     => 'canonical'
        );
    }

    public function testSingleRenderFlags()
    {
        $active = $this->helper->findOneByLabel('Home');
        $active->active = true;

        $expected = array();
        $actual   = array();

        // build expected and actual result
        foreach ($this->_getFlags() as $newFlag => $type) {
            // add forced relation
            $active->addRel($type, 'http://www.example.com/');
            $active->addRev($type, 'http://www.example.com/');

            $this->helper->setRenderFlag($newFlag);
            $expectedOutput = '<link '
                              . 'rel="' . $type . '" '
                              . 'href="http://www.example.com/">' . constant($this->helperName.'::EOL')
                            . '<link '
                              . 'rev="' . $type . '" '
                              . 'href="http://www.example.com/">';
            $actualOutput = $this->helper->render();

            $expected[$type] = $expectedOutput;
            $actual[$type]   = $actualOutput;

            // remove forced relation
            $active->removeRel($type);
            $active->removeRev($type);
        }

        $this->assertEquals($expected, $actual);
    }

    public function testRenderFlagBitwiseOr()
    {
        $newFlag = Navigation\Links::RENDER_NEXT |
                   Navigation\Links::RENDER_PREV;
        $this->helper->setRenderFlag($newFlag);
        $active = $this->helper->findOneByLabel('Page 1.1');
        $active->active = true;

        // test data
        $expected = '<link rel="next" href="page2" title="Page 2">'
                  . constant($this->helperName.'::EOL')
                  . '<link rel="prev" href="page1" title="Page 1">';
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testIndenting()
    {
        $active = $this->helper->findOneByLabel('Page 1.1');
        $newFlag = Navigation\Links::RENDER_NEXT |
                   Navigation\Links::RENDER_PREV;
        $this->helper->setRenderFlag($newFlag);
        $this->helper->setIndent('  ');
        $active->active = true;

        // build expected and actual result
        $expected = '  <link rel="next" href="page2" title="Page 2">'
                  . constant($this->helperName.'::EOL')
                  . '  <link rel="prev" href="page1" title="Page 1">';
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testSetMaxDepth()
    {
        $this->helper->setMaxDepth(1);
        $this->helper->findOneByLabel('Page 2.3.3')->setActive(); // level 2
        $flag = Navigation\Links::RENDER_NEXT;

        $expected = '<link rel="next" href="page2/page2_3/page2_3_1" title="Page 2.3.1">';
        $actual = $this->helper->setRenderFlag($flag)->render();

        $this->assertEquals($expected, $actual);
    }

    public function testSetMinDepth()
    {
        $this->helper->setMinDepth(2);
        $this->helper->findOneByLabel('Page 2.3')->setActive(); // level 1
        $flag = Navigation\Links::RENDER_NEXT;

        $expected = '';
        $actual = $this->helper->setRenderFlag($flag)->render();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Returns the contens of the expected $file, normalizes newlines
     * @param  string $file
     * @return string
     */
    protected function _getExpected($file)
    {
        return str_replace("\n", PHP_EOL, parent::_getExpected($file));
    }
}
