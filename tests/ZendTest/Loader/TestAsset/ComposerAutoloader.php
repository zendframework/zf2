<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Loader\TestAsset;

use Zend\Loader\ComposerAutoloader as RealComposerAutoloader;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ComposerAutoloader extends RealComposerAutoloader
{
    public function getNamespaces()
    {
        return $this->composerAutoloader->getPrefixes();
    }

    public function getNamespacesPsr4()
    {
        return $this->composerAutoloader->getPrefixesPsr4();
    }

    public function getClassMap()
    {
        return $this->composerAutoloader->getClassMap();
    }
}
