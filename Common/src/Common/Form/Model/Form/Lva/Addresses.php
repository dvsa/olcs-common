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
     * @Form\Options({"label":"application_your-business_business-type.correspondence.label"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Correspondence")
     */
    public $correspondence = null;

    /**
     * @Form\Name("correspondence_address")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     */
    public $correspondenceAddress = null;

    /**
     * @Form\Name("contact")
     * @Form\Options({
     *     "label": "application_your-business_business-type.contact-details.label",
     *     "hint": "application_your-business_business-type.contact-details.hint"
     * })
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Contact")
     */
    public $contact = null;

    /**
     * @Form\Name("establishment")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Establishment")
     */
    public $establishment = null;

    /**
     * @Form\Name("establishment_address")
     * @Form\Options({"label":"application_your-business_business-type.establishment.label"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     */
    public $establishmentAddress = null;

    /**
     * @Form\Name("registered_office")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\RegisteredOffice")
     */
    public $registeredOffice = null;

    /**
     * @Form\Name("registered_office_address")
     * @Form\Options({"label":"application_your-business_business-type.registered-office.label"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     */
    public $registeredOfficeAddress = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     */
    public $formActions = null;
}
