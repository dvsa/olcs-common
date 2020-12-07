<?php

namespace Common\Form\Model\Form\Lva;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 * @Form\Name("upload-evidence")
 * @Form\Options({"prefer_form_input_filter":true})
 */
class UploadEvidence
{
    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\UploadEvidenceFinancialEvidence")
     * @Form\Options({
     *    "label": "lva.section.title.upload-evidence.financial-evidence"
     * })
     * @Form\Name("financialEvidence")
     */
    public $financialEvidence = null;

    /**
     * @Form\ComposedObject({
     *      "target_object":"Common\Form\Model\Form\Lva\Fieldset\UploadEvidenceOperatingCentre",
     *      "is_collection":true,
     *      "options":{
     *          "count": 1,
     *          "label":"lva.section.title.upload-evidence.operating-centres",
     *      }
     * })
     * @Form\Name("operatingCentres")
     */
    public $operatingCentres = null;

    /**
     * @Form\ComposedObject({
     *      "target_object":"Common\Form\Model\Form\Lva\Fieldset\UploadEvidenceSupportingEvidence",
     *      "options":{
     *          "count": 1,
     *          "label":"lva.section.title.upload-evidence.supporting-evidence",
     *      }
     * })
     * @Form\Name("supportingEvidence")
     */
    public $supportingEvidence = null;


    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label": "Save and continue"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $saveAndContinue = null;
}
