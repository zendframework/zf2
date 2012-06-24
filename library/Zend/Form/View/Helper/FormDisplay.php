<?php

namespace Zend\Form\View\Helper;

use Zend\Form\Form as zendForm;

class FormDisplay extends AbstractHelper {
	/**
	 * Retrieve a printable form string
	 *
	 * @param zendForm $form
	 * @return string
	 */
	public function __invoke(zendForm $form, $renderer = null) {
		if (is_null($renderer)) {
			$renderer = $this->getView()->formtable();
		}
		
		$result = '';
		
		// Render the opening tag
		$result .= $this->getView()->form()->openTag($form);
		
		foreach ($form->getFieldsets() as $fieldSet) { /* @var $fieldSet Fieldset */
			$result .= '<fieldset><legend>'.$fieldSet->getName().'</legend>';
			
			$result .= $renderer->openTag();
			
			foreach ($fieldSet->getElements() as $element) { /* @var $element Element */
				$result .= $renderer->element($element);
			}
			
			$result .= $renderer->closeTag();
			
			$result .= '</fieldset>';
		}

		$result .= $renderer->openTag();
		
		foreach ($form->getElements() as $element) { /* @var $element Element */
			$result .= $renderer->element($element);
		}
		
		$result .= $renderer->closeTag();
		
		$result .= $this->getView()->form()->closeTag($form);
		
		return $result;
	}
}	