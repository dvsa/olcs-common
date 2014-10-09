<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_vehicle-safety_undertakings")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationVehicleSafetyUndertakings
{

    /**
     * @Form\Name("application")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationVehicleSafetyUndertakingsApplication")
     */
    public $application = null;

    /**
     * @Form\Name("smallVehiclesIntention")
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings-smallVehiclesUndertakings"
     * })
     * @Form\ComposedObject("Common\Form\Model\Fieldset\SmallVehiclesIntention")
     */
    public $smallVehiclesIntention = null;

    /**
     * @Form\Name("nineOrMore")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\NineOrMore")
     */
    public $nineOrMore = null;

    /**
     * @Form\Name("limousinesNoveltyVehicles")
     * @Form\Options({"label":"application_vehicle-safety_undertakings-limousines"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\LimousinesNoveltyVehicles")
     */
    public $limousinesNoveltyVehicles = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

