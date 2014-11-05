<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_vehicle-safety_vehicle-psv")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class PsvVehicles
{
    /**
     * @Form\Name("data")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\PsvVehiclesData")
     */
    public $data = null;

    /**
     * @Form\Name("small")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Small")
     */
    public $small = null;

    /**
     * @Form\Name("medium")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Medium")
     */
    public $medium = null;

    /**
     * @Form\Name("large")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Large")
     */
    public $large = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     */
    public $formActions = null;
}
