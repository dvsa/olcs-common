<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class UploadEvidence
{
    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\UploadEvidenceFinancialEvidence")
     * @Form\Options({
     *    "label": "lva.section.title.upload-evidence.financial-evidence"
     * })
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
     */
    public $operatingCentres = null;


    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label": "Save and continue"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $saveAndContinue = null;
}
