<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator\View\Helper;

use Zend\Paginator\Paginator;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Exception;

class PaginationControl extends AbstractHelper
{
    /**
     * Default Scrolling Style
     *
     * @var string
     */
    protected $defaultScrollingStyle = 'sliding';

    /**
     * Default view partial
     *
     * @var string|array
     */
    protected $defaultViewPartial = null;

    /**
     * Render the provided pages.  This checks if $view->paginator is set and,
     * if so, uses that.  Also, if no scrolling style or partial are specified,
     * the defaults will be used (if set).
     *
     * @param  Paginator           $paginator
     * @param  string              $scrollingStyle (Optional) Scrolling style
     * @param  string              $partial        (Optional) View partial
     * @param  array|string        $params         (Optional) params to pass to the partial
     * @throws Exception\RuntimeException if no paginator or no view partial provided
     * @throws Exception\InvalidArgumentException if partial is invalid array
     * @return string
     */
    public function __invoke(Paginator $paginator, $scrollingStyle = null, $partial = null, $params = null)
    {
        if ($partial === null) {
            if ($this->defaultViewPartial === null) {
                throw new Exception\RuntimeException('No view partial provided and no default set');
            }

            $partial = $this->defaultViewPartial;
        }

        if ($scrollingStyle === null) {
            $scrollingStyle = $this->defaultScrollingStyle;
        }

        $pages = get_object_vars($paginator->getPages($scrollingStyle));

        if ($params !== null) {
            $pages = array_merge($pages, (array) $params);
        }

        $partialHelper = $this->view->plugin('partial');
        return $partialHelper($partial, $pages);
    }

    /**
     * Sets the default Scrolling Style
     *
     * @param string $style string 'all' | 'elastic' | 'sliding' | 'jumping'
     * @return self
     */
    public function setDefaultScrollingStyle($style)
    {
        $this->defaultScrollingStyle = $style;

        return $this;
    }

    /**
     * Gets the default scrolling style
     *
     * @return string
     */
    public function getDefaultScrollingStyle()
    {
        return $this->defaultScrollingStyle;
    }

    /**
     * Sets the default view partial.
     *
     * @param string|array $partial View partial
     * @return self
     */
    public function setDefaultViewPartial($partial)
    {
        $this->defaultViewPartial = $partial;

        return $this;
    }

    /**
     * Gets the default view partial
     *
     * @return string|array
     */
    public function getDefaultViewPartial()
    {
        return $this->defaultViewPartial;
    }
}
