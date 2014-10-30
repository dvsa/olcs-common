<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("case")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class CaseForm
{
    /**
     * @Form\Name("submissionSections")
     * @Form\Options({"label":"Select one or more categories"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\SubmissionSections")
     */
    public $submissionSections = null;

    /**
     * @Form\Name("fields")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Fields")
     */
    public $fields = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\CaseFormActions")
     */
    public $formActions = null;
}
