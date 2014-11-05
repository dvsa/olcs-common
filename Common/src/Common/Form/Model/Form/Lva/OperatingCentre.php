<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class OperatingCentre
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
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentreData")
     */
    public $data = null;

    /**
     * @Form\Name("advertisements")
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation-sub-action.advertisements"
     * })
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Advertisements")
     */
    public $advertisements = null;

    /**
     * @Form\Name("trafficArea")
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $trafficArea = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormCrudActions")
     */
    public $formActions = null;

    /**
     * @Form\Name("operatingCentre")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentre")
     */
    public $operatingCentre = null;
}
