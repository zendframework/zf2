<?php
namespace Zend\Form\View\Helper\Renderer;

use Zend\Form\Element;

interface RendererInterface
{
	/**
	 * @return string
	 */
	public function openTag();
	
	/**
	 * @return string
	 */
	public function closeTag();
	
	/**
	 * @param Element $element
	 * @return string
	 */
	public function element(Element $element);
}
