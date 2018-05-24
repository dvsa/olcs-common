<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("YesNoRadio")
 * @Form\Attributes({"class":"radio-button__fieldset"})
 */
class YesNoRadio
{
    /**
     * @Form\Required(true)
     * @Form\Type("Common\Form\Elements\Types\RadioYesNo")
     * @Form\Options({
     *     "error-message": "internal-delete-final-tm-letter-opt-out.validation-message",
     *      "label_attributes": {"class": "form-control form-control--radio form-control--inline"},
     * })
     */
    public $yesNo = null;
}
