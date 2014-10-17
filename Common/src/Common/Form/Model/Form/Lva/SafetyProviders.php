<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-safety-providers")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class SafetyProviders
{
    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\SafetyProvidersData")
     */
    public $data = null;

    /**
     * @Form\Name("contactDetails")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\SafetyProvidersContactDetails")
     */
    public $contactDetails = null;

    /**
     * @Form\Name("address")
     * @Form\Options({"label":"application_vehicle-safety_safety-sub-action.address.label"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     */
    public $address = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormCrudActions")
     */
    public $formActions = null;
}
