<?php

namespace Common\Form\Model\Form\Lva;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-vehicles-declarations-evidence-small")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class VehiclesDeclarationsEvidenceSmall
{
    /**
     * @Form\Name("finance")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FinancialEvidenceFinance")
     */
    public $finance;

    /**
     * @Form\Name("evidence")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FinancialEvidenceEvidence")
     * @Form\Options({
     *    "label": "lva-financial-evidence-evidence.label",
     *    "hint": "lva-financial-evidence-evidence.hint"
     * })
     */
    public $evidence;

    /**
     * @Form\Name("version")
     * @Form\Type("Hidden")
     */
    public $version;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     * @Form\Attributes({"class":"govuk-button-group"})
     */
    public $formActions;
}
