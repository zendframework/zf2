<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Text_Figlet
 */

namespace Zend\Text\Figlet;

use Zend\Stdlib\Options;

/**
 * Options for Zend\Text\Figlet
 *
 * @category  Zend
 * @package   Zend_Text_Figlet
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class FigletOptions extends Options
{
    /**
     * Text alignment
     */
    const ALIGN_LEFT   = 0;
    const ALIGN_CENTER = 1;
    const ALIGN_RIGHT  = 2;

    /**
     * Print direction
     */
    const DIRECTION_LTR = 0;
    const DIRECTION_RTL = 1;

    /**
     * Smush2 layout modes
     */
    const SM_EQUAL     = 0x01;
    const SM_LOWLINE   = 0x02;
    const SM_HIERARCHY = 0x04;
    const SM_PAIR      = 0x08;
    const SM_BIGX      = 0x10;
    const SM_HARDBLANK = 0x20;
    const SM_KERN      = 0x40;
    const SM_SMUSH     = 0x80;

    /**
     * Smush mode override modes
     */
    const SMO_NO    = 0;
    const SMO_YES   = 1;
    const SMO_FORCE = 2;

    /**
     * @var Font
     */
    protected $font = null;

    protected $outputWidth = 80;

    protected $handleParagraphs = false;

    protected $direction = self::DIRECTION_LTR;

    protected $align = null;

    protected $smushMode = 0;

    /**
     * Override font file smush layout
     *
     * @var integer
     */
    protected $smushOverride = self::SMO_NO;

    /**
     * Set a font to use
     *
     * @param  Font|string
     * @return Figlet
     * @throws Exception\InvalidArgumentException
     */
    public function setFont($font)
    {
        if (!is_string($font) && ! $font instanceof Font) {
            throw new Exception\InvalidArgumentException(
                'Parameter should be path to font file or instance of Zend\Text\Figlet\Font'
            );
        }

        if (is_string($font)) {
            $this->font = new Font($font);
        } else {
            $this->font = $font;
        }

        return $this;
    }

    /**
     * get FIGlet font
     *
     * @return Font
     */
    public function getFont()
    {
        if (null === $this->font) {
            $this->font = new Font();
        }
        return $this->font;
    }


    /**
     * Set handling of paragraphs
     *
     * @param  boolean $handleParagraphs Wether to handle paragraphs or not
     * @return FigletOptions
     */
    public function setHandleParagraphs($handleParagraphs)
    {
        $this->handleParagraphs = (bool) $handleParagraphs;
        return $this;
    }

    public function getHandleParagraphs()
    {
        return $this->handleParagraphs;
    }

    /**
     * Set the output width
     *
     * @param  integer $outputWidth Output with which should be used for word
     *                              wrapping and justification
     * @return FigletOptions
     */
    public function setOutputWidth($outputWidth)
    {
        $this->outputWidth = max(1, (int) $outputWidth);
        return $this;
    }

    public function getOutputWidth()
    {
        return $this->outputWidth;
    }

    /**
     * Set print direction
     *
     * @param int $direction
     * @return FigletOptions
     */
    public function setDirection($direction)
    {
        $this->direction = min(self::DIRECTION_RTL, max(self::DIRECTION_LTR, (int) $direction));
        return $this;
    }

    /**
     * Get print direction
     *
     * @return int
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set text alignment
     *
     * @param int $align
     * @return FigletOptions
     */
    public function setAlign($align)
    {
        $this->align = min(self::ALIGN_RIGHT, max(self::ALIGN_LEFT, (int) $align));
        return $this;
    }

    /**
     * Get text alignment
     *
     * @return int
     */
    public function getAlign()
    {
        // if not set, set alignment based on direction
        if ($this->align === null) {
            $this->align = 2 * $this->direction;
        }
        return $this->align;
    }

    /**
     * Set the smush mode.
     *
     * Use one of the constants of Zend\Text\FigletOptions::SM_*,
     * you may combine them.
     *
     * @param  integer $smushMode Smush mode to use for generating text
     * @return Figlet
     */
    public function setSmushMode($smushMode)
    {
        $smushMode = (int) $smushMode;
        $fontSmush = $this->getFont()->getParam('full_layout');
        $userSmush = 0;

        if ($smushMode < -1) {
            $this->smushOverride = self::SMO_NO;
        } else {
            if ($smushMode === 0) {
                $userSmush = self::SM_KERN;
            } else if ($smushMode === -1) {
                $userSmush = 0;
            } else {
                $userSmush = (($smushMode & 63) | self::SM_SMUSH);
            }

            $this->smushOverride = self::SMO_YES;
        }

        if ($this->smushOverride === self::SMO_NO) {
            $this->smushMode = $fontSmush;
        } elseif ($this->smushOverride === self::SMO_YES) {
            $this->smushMode = $userSmush;
        } elseif ($this->smushOverride === self::SMO_FORCE) {
            $this->smushMode = ($fontSmush | $userSmush);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getSmushMode()
    {
        return $this->smushMode;
    }

    /**
     * Set whether user smushing settings override font settings
     *
     * @param int $override
     * @return FigletOptions
     */
    public function setSmushOverride($override)
    {
        $this->smushOverride = min(self::SMO_FORCE, max(self::SMO_NO, (int) $override));
        return $this;
    }

    /**
     * Get whether user smushing settings override font settings
     *
     * @return int
     */
    public function getSmushOverride()
    {
        return $this->smushOverride;
    }

}
