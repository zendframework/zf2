<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form\View\Helper\Captcha;

use Zend\Captcha\Dumb as CaptchaAdapter;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

class Dumb extends AbstractWord
{
    protected static $defaultTemplate = 'Please type this word backwards <b>%s</b>';
    
    public static function setDefaultTemplate($template)
    {
        static::$defaultTemplate = $template;
    }
    
    public static function getDefaultTemplate()
    {
        return static::$defaultTemplate;
    }

    /**
     * Render the captcha
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $captcha = $element->getCaptcha();

        if ($captcha === null || !$captcha instanceof CaptchaAdapter) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute of type Zend\Captcha\Dumb; none found',
                __METHOD__
            ));
        }

        $captcha->generate();
        
        $template = $element->getLabel() ?: static::getDefaultTemplate();
        if (null !== ($translator = $this->getTranslator())) {
            $template = $translator->translate($template, $this->getTranslatorTextDomain());
        }

        $label = $this->renderRiddle($template, $captcha->getWord());

        $position     = $this->getCaptchaPosition();
        $separator    = $this->getSeparator();
        $captchaInput = $this->renderCaptchaInputs($element);

        $pattern = '%s%s%s';
        if ($position === self::CAPTCHA_PREPEND) {
            return sprintf($pattern, $captchaInput, $separator, $label);
        }

        return sprintf($pattern, $label, $separator, $captchaInput);
    }
    
    public function renderRiddle($template, $word)
    {
        return sprintf($template, strrev($word));
    }
}
