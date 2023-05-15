<?php

namespace Common\Form\Model\Form\Lva;

use Laminas\Form\Annotation as Form;
use Common\Form\Model\Form\Traits\VersionTrait;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-business-details")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class BusinessDetails
{
    use VersionTrait;

    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\BusinessDetails")
     */
    public $data = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\RegisteredAddress")
     * @Form\Options({"label": "application_your-business_business-details.data.registered_address"})
     */
    public $registeredAddress = null;

    /**
     * @Form\Name("table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({"id":"table"})
     */
    public $table = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\BusinessDetailsAllowEmail")
     * @Form\Name("allow-email")
     */
    public $allowEmail = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     * @Form\Attributes({"class":"govuk-button-group"})
     */
    public $formActions = null;
}
