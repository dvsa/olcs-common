<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("generic-delete-confirmation")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class GenericDeleteConfirmation
{
    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\DeleteConfirmButtons")
     */
    public $formActions = null;
}
