<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_vehicle-safety_safety-sub-action")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationVehicleSafetySafetySubAction
{

    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationVehicleSafetySafetySubActionData")
     */
    public $data = null;

    /**
     * @Form\Name("contactDetails")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationVehicleSafetySafetySubActionContactDetails")
     */
    public $contactDetails = null;

    /**
     * @Form\Name("address")
     * @Form\Options({"label":"application_vehicle-safety_safety-sub-action.address.label"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationVehicleSafetySafetySubActionAddress")
     */
    public $address = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyCrudButtons")
     */
    public $formActions = null;


}

