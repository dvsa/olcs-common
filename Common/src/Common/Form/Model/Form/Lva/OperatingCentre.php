<?php

namespace Common\Form\Model\Form\Lva;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class OperatingCentre
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

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
     *     "label": "application_operating-centres_authorisation-sub-action.advertisements",
     *     "error-messages": "test",
     *     "fieldset-data-group": "advertisements",
     * })
     */
    public $advertisements = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormCrudActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
