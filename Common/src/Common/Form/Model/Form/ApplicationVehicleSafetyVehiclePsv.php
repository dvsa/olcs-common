<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_vehicle-safety_vehicle-psv")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationVehicleSafetyVehiclePsv
{

    /**
     * @Form\Name("data")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationVehicleSafetyVehiclePsvData")
     */
    public $data = null;

    /**
     * @Form\Name("small")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Small")
     */
    public $small = null;

    /**
     * @Form\Name("medium")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Medium")
     */
    public $medium = null;

    /**
     * @Form\Name("large")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Large")
     */
    public $large = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

