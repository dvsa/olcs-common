<?php

namespace Common\Form\Model\Form\Lva;

use Laminas\Form\Annotation as Form;

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
    public $version;

    /**
     * @Form\Name("psvVehicleSize")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\PsvVehiclesSize")
     */
    public $psvVehicleSize;

    /**
     * @Form\Name("smallVehiclesIntention")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\VehiclesDeclarationsSmallVehiclesIntention")
     */
    public $smallVehiclesIntention;

    /**
     * @Form\Name("nineOrMore")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\VehiclesDeclarationsNineOrMore")
     * @Form\Options({"label":"application_vehicle-safety_undertakings-nineOrMore"})
     */
    public $nineOrMore;

    /**
     * @Form\Name("mainOccupation")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\VehiclesDeclarationsMainOccupation")
     * @Form\Options({"label":"application_vehicle-safety_undertakings.mainOccupation"})
     */
    public $mainOccupation;

    /**
     * @Form\Name("limousinesNoveltyVehicles")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\VehiclesDeclarationsLimousinesNoveltyVehicles")
     * @Form\Options({"label":"application_vehicle-safety_undertakings-limousines"})
     */
    public $limousinesNoveltyVehicles;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     * @Form\Attributes({"class":"govuk-button-group"})
     */
    public $formActions;
}
