<?php
namespace Zend\Form\View\Helper\Renderer;

use Zend\View\Helper\AbstractHelper,
	Zend\Form\Element;

class Table extends AbstractHelper implements RendererInterface {

	/**
	 * @return Table
	 */
	public function __invoke() {
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function openTag() {
		return '<table class="zend-form-table">';
	}
	
	/**
	 * @return string
	 */
	public function closeTag() {
		return '</table>';
	}
	
	/**
	 * @param Element $element
	 * @return string
	 */
	public function element(Element $element) {
		$label = is_null($element->getAttribute('label')) ? '' : $this->getView()->FormLabel($element);	
		$value = $this->getView()->FormElement($element);
		$errors = $this->getView()->FormElementErrors($element);
		
		return <<<ELEMENT
<tr>
	<td>{$label}</td>
	<td>{$value}</td>
	<td>{$errors}</td>
</tr>
ELEMENT;
	}
}