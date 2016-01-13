<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-financial-evidence")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class FinancialEvidence
{
    /**
     * @Form\Name("finance")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FinancialEvidenceFinance")
     */
    public $finance = null;

    /**
     * @Form\Name("evidence")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FinancialEvidenceEvidence")
     * @Form\Options({"label": "lva-financial-evidence-evidence.label"})
     */
    public $evidence = null;

    /**
     * @Form\Name("sendByPost")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FinancialEvidencePost")
     */
    public $sendByPost = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;
}
