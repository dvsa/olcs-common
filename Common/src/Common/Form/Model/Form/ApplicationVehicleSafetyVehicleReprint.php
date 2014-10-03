<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_vehicle-safety_vehicle-reprint")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationVehicleSafetyVehicleReprint
{

    /**
     * @Form\Name("data")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationVehicleSafetyVehicleReprintData")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons-2")
     */
    public $formActions = null;


}

