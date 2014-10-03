<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_operating-centres_authorisation-sub-action")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationOperatingCentresAuthorisationSubAction
{

    /**
     * @Form\Name("address")
     * @Form\Options({"label":"Address"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     */
    public $address = null;

    /**
     * @Form\Name("data")
     * @Form\Options({"label":"application_operating-centres_authorisation-sub-action.data"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Data")
     */
    public $data = null;

    /**
     * @Form\Name("advertisements")
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation-sub-action.advertisements"
     * })
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Advertisements")
     */
    public $advertisements = null;

    /**
     * @Form\Name("trafficArea")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TrafficArea")
     */
    public $trafficArea = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyCrudButtons")
     */
    public $formActions = null;

    /**
     * @Form\Name("operatingCentre")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\OperatingCentre")
     */
    public $operatingCentre = null;


}

