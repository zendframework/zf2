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
 * @category   Zend
 * @package    Reader\Reader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
* @namespace
*/
namespace Zend\Feed\Reader\Extension\Content;
use Zend\Feed\Reader;
use Zend\Feed\Reader\Extension;

/**
* @uses \Zend\Feed\Reader\Reader
* @uses Reader\Reader_Entry_EntryAbstract
* @category Zend
* @package Reader\Reader
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
*/
class Entry extends Extension\AbstractEntry
{

    public function getContent()
    {
        if ($this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $content = $this->_xpath->evaluate('string('.$this->getXpathPrefix().'/content:encoded)');
        } else {
            $content = $this->_xpath->evaluate('string('.$this->getXpathPrefix().'/content:encoded)');
        }
        return $content;
    }

    /**
     * Register RSS Content Module namespace
     */
    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
    }
}
