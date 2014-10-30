<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_vehicle-safety_vehicle-psv-sub-action")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class PsvVehiclesVehicle
{
    /**
     * @Form\Name("data")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\PsvVehicleData")
     */
    public $data = null;

    /**
     * @Form\Name("licence-vehicle")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceVehicle")
     */
    public $licenceVehicle = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     */
    public $formActions = null;
}
