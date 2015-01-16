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
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentres")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data",
     *     "hint": "application_operating-centres_authorisation.data.hint"
     * })
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
