<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-addresses")
 * @Form\Type("Common\Form\Form")
 */
class Addresses
{
    /**
     * @Form\Name("correspondence")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Correspondence")
     * @Form\Options({"label":"application_your-business_business-type.correspondence.label"})
     */
    public $correspondence = null;

    /**
     * @Form\Name("correspondence_address")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     */
    public $correspondenceAddress = null;

    /**
     * @Form\Name("contact")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Contact")
     * @Form\Options({
     *     "label": "application_your-business_business-type.contact-details.label",
     *     "hint": "application_your-business_business-type.contact-details.hint"
     * })
     */
    public $contact = null;

    /**
     * @Form\Name("establishment")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Establishment")
     */
    public $establishment = null;

    /**
     * @Form\Name("establishment_address")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     * @Form\Options({"label":"application_your-business_business-type.establishment.label"})
     */
    public $establishmentAddress = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
