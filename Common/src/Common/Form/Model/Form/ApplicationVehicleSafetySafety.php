<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_vehicle-safety_safety")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationVehicleSafetySafety
{

    /**
     * @Form\Name("licence")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Licence")
     */
    public $licence = null;

    /**
     * @Form\Name("table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationVehicleSafetySafetyTable")
     */
    public $table = null;

    /**
     * @Form\Name("application")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Application")
     */
    public $application = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

