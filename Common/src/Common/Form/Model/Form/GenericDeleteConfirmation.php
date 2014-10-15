<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("generic-delete-confirmation")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class GenericDeleteConfirmation
{

    /**
     * @Form\Name("data")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\GenericDeleteConfirmationData")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyDeleteConfirmButtons")
     */
    public $formActions = null;
}
