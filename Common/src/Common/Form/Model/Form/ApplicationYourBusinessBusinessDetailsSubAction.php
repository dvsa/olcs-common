<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_your-business_business-details-sub-action")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationYourBusinessBusinessDetailsSubAction
{

    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationYourBusinessBusinessDetailsSubActionData")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyCrudButtons")
     */
    public $formActions = null;


}

