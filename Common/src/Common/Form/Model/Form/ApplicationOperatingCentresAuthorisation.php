<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_operating-centres_authorisation")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationOperatingCentresAuthorisation
{

    /**
     * @Form\Name("table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $table = null;

    /**
     * @Form\Name("dataTrafficArea")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\DataTrafficArea")
     */
    public $dataTrafficArea = null;

    /**
     * @Form\Name("data")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data",
     *     "hint": "application_operating-centres_authorisation.data.hint"
     * })
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationOperatingCentresAuthorisationData")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

