<?php

namespace Common\Form\Model\Form\Lva\Fieldset\TransportManager;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"class":"table__form"})
 * @Form\Name("otherEmployments")
 */
class OtherEmployments
{
    /**
     * @Form\Options({
     *     "label": "transport-manager.employment.form.radio.label",
     *     "hint" : "transport-manager.employment.form.radio.hint",
     *     "hint-class" : "",
     *     "value_options": {"Y":"Yes", "N":"No"},
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"}
     * })
     * @Form\Type("Radio")
     */
    public $hasOtherEmployment = null;

    /**
     * @Form\Name("otherEmployment")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({
     *      "id":"otherEmployments",
     *      "class": "help__text help__text--removePadding"
     * })
     */
    public $otherEmployment = null;
}
