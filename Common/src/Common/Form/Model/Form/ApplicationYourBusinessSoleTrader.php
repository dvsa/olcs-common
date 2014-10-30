<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_your-business_sole-trader")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationYourBusinessSoleTrader
{
    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationYourBusinessSoleTraderData")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;
}
