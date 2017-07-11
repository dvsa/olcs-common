<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("licenceChecklistConfirmationYes")
 */
class LicenceChecklistConfirmationYes
{
    /**
     * @Form\Attributes({"value": "markup-continuation-licence-checklist-confirmation-yes"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $checklistConfirmText = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label":"continuations.checklist.confirmation.yes-button"})
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $submit = null;
}
