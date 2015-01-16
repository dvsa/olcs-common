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
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     * @Form\Options({"label":"Address"})
     */
    public $address = null;

    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentreData")
     * @Form\Options({"label":"application_operating-centres_authorisation-sub-action.data"})
     */
    public $data = null;

    /**
     * @Form\Name("advertisements")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\Advertisements")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.advertisements"
     * })
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
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormCrudActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;

    /**
     * @Form\Name("operatingCentre")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentre")
     */
    public $operatingCentre = null;
}
