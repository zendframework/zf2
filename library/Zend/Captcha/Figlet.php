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
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Captcha;

use Zend\Text\Figlet\Figlet as FigletManager;
use Zend\Text\Figlet\FigletOptions;
use Traversable;

/**
 * Captcha based on figlet text rendering service
 *
 * Note that this engine seems not to like numbers
 *
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Figlet extends Word
{
    /**
     * Figlet text renderer
     *
     * @var FigletManager
     */
    protected $figlet;

    /**
     * Constructor
     *
     * @param  null|string|array|\Traversable $options
     * @return void
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        // setup Figlet instance
        $figletOptions = null;
        if (is_array($options) || $options instanceof Traversable) {
            $figletOptions      = new FigletOptions();
            $figletOptionsArray = $figletOptions->toArray();
            foreach ($options as $name => $value) {
                if (isset($figletOptionsArray[$name])) {
                    $figletOptionsArray[$name] = $value;
                }
            }
            $figletOptions = new FigletOptions($figletOptionsArray);
        }
        $this->figlet = new FigletManager($figletOptions);
    }

    /**
     * Retrieve the composed figlet manager
     * 
     * @return FigletManager
     */
    public function getFiglet()
    {
        return $this->figlet;
    }

    /**
     * Generate new captcha
     *
     * @return string
     */
    public function generate()
    {
        $this->useNumbers = false;
        return parent::generate();
    }

    /**
     * Get helper name used to render captcha
     * 
     * @return string
     */
    public function getHelperName()
    {
        return 'captcha/figlet';
    }
}
