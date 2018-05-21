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
     * @Form\Attributes({"class":"radio-button__container radio-button__container--inline"})
     * @Form\Options({
     *     "empty_option": "Please select",
     *     "label": "internal-delete.final-tm.confirmation-info.text",
     *     "label_attributes": {"class": "form-element__label"},
     *     "value_options":{0:"Yes", 1:"No"},
     * })
     * @Form\Validator({
     *     "name": "Zend\Validator\NotEmpty",
     *     "options": {
     *         "allow_empty": false,
     *         "messages": {
     *             "isEmpty": "transport-manager.other-licence.form.hours-per-week.error_msg",
     *         }
     *     }
     * })
     */
    public $yesNo = null;
}