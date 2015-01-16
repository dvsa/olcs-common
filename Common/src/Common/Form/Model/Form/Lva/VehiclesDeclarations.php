<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-vehicles-declarations")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class VehiclesDeclarations
{
    /**
     * @Form\Name("version")
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Name("smallVehiclesIntention")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\VehiclesDeclarationsSmallVehiclesIntention")
     */
    public $smallVehiclesIntention = null;

    /**
     * @Form\Name("nineOrMore")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\VehiclesDeclarationsNineOrMore")
     * @Form\Options({"label":"application_vehicle-safety_undertakings-nineOrMore"})
     */
    public $nineOrMore = null;

    /**
     * @Form\Name("limousinesNoveltyVehicles")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\VehiclesDeclarationsLimousinesNoveltyVehicles")
     * @Form\Options({"label":"application_vehicle-safety_undertakings-limousines"})
     */
    public $limousinesNoveltyVehicles = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
