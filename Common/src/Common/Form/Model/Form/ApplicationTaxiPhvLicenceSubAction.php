<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_taxi-phv_licence-sub-action")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationTaxiPhvLicenceSubAction
{

    /**
     * @Form\Name("data")
     * @Form\Options({"label":"application_taxi-phv_licence-sub-action.data"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationTaxiPhvLicenceSubActionData")
     */
    public $data = null;

    /**
     * @Form\Name("contactDetails")
     * @Form\Options({"label":"application_taxi-phv_licence-sub-action.contactDetails"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ContactDetails")
     */
    public $contactDetails = null;

    /**
     * @Form\Name("address")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationTaxiPhvLicenceSubActionAddress")
     */
    public $address = null;

    /**
     * @Form\Name("trafficArea")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TrafficArea")
     */
    public $trafficArea = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyCrudButtons")
     */
    public $formActions = null;


}

