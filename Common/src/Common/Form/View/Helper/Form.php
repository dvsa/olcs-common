<?php
namespace Common\Form\View\Helper;

use Zend\Form\FormInterface as ZendFormInterface;
use Common\Form\View\Helper\Traits as AlphaGovTraits;
use Zend\Form\FieldsetInterface;

class Form extends \Zend\Form\View\Helper\Form
{
    use AlphaGovTraits\Logger;

    /**
     * Render a form from the provided $form
     *  We override the parent here as we want to
     *   a. Add logging
     *   b. Ensure fieldsets come before elements
     *
     * @param  ZendFormInterface $form
     * @return string
     */
    public function render(ZendFormInterface $form)
    {
        $this->log('Rendering Form', LOG_INFO);

        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $fieldsets = $elements = array();

        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $fieldsets[] = $this->getView()->formCollection($element);
            } else {
                $elements[] = $this->getView()->formRow($element);
            }
        }

        return $this->openTag($form) . implode("\n", $fieldsets) . implode("\n", $elements) . $this->closeTag();
    }
}
