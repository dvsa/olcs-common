<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class OperatingCentres
{
    /**
     * @Form\Name("table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $table = null;

    /**
     * @Form\Name("dataTrafficArea")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TrafficArea")
     */
    public $dataTrafficArea = null;

    /**
     * @Form\Name("data")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data",
     *     "hint": "application_operating-centres_authorisation.data.hint"
     * })
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentres")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     */
    public $formActions = null;
}
