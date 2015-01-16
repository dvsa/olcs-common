<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-safety")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class Safety
{
    /**
     * @Form\Name("licence")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\SafetyLicence")
     */
    public $licence = null;

    /**
     * @Form\Name("table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TableRequired")
     */
    public $table = null;

    /**
     * @Form\Name("application")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\SafetyApplication")
     */
    public $application = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
