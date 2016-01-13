<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_your-business_business-details-sub-action")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class BusinessDetailsSubsidiaryCompany
{
    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\SubsidiaryCompany")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormCrudActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
