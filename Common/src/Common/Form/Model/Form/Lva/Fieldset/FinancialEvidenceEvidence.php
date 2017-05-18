<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("evidence")
 * @Form\Attributes({"class":"last"})
 */
class FinancialEvidenceEvidence
{
    /**
     * @Form\Required(false)
     * @Form\Attributes({"id":"uploadNowRadio","allowWrap":true,"data-container-class":"form-control__container"})
     * @Form\Options({
     *     "legend-attributes": {"class": "visually-hidden"},
     *     "label": "lva-financial-evidence-upload-now.label",
     *     "label_attributes": {"class": "form-control form-control--radio"},
     *     "value_options": {"1":"lva-financial-evidence-upload-now.yes"},
     *     "error-message": "financialEvidence_uploadNow-error"
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $uploadNowRadio = null;

    /**
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\MultipleFileUpload")
     * @Form\Attributes({"id":"files"})
     */
    public $files = null;

    /**
     * @Form\Required(false)
     * @Form\Attributes({"id":"sendByPost","allowWrap":true,"data-container-class":"form-control__container"})
     * @Form\Options({
     *     "legend-attributes": {"class": "visually-hidden"},
     *     "label": "lva-financial-evidence-upload-now.label",
     *     "label_attributes": {"class": "form-control form-control--radio"},
     *     "value_options": {"0":"lva-financial-evidence-upload-now.no"},
     *     "error-message": "financialEvidence_uploadNow-error"
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $sendByPostRadio = null;

    /**
     * @Form\Name("sendByPost")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FinancialEvidencePost")
     */
    public $sendByPost = null;

    /**
     * @Form\Required(false)
     * @Form\Attributes({"id":"uploadLaterRadio","allowWrap":true,"data-container-class":"form-control__container"})
     * @Form\Options({
     *     "legend-attributes": {"class": "visually-hidden",},
     *     "label": "lva-financial-evidence-upload-now.label",
     *     "label_attributes": {"class": "form-control form-control--radio"},
     *     "value_options": {"2":"lva-financial-evidence-upload-now.later"},
     *     "error-message": "financialEvidence_uploadNow-error"
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $uploadLaterRadio = null;

    /**
     * @Form\Name("uploadLater")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FinancialEvidenceUploadLater")
     */
    public $uploadLater = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Options({
     *     "fieldset-attributes": {
     *         "id": "files",
     *     },
     * })
     * @Form\Required(true)
     * @Form\Attributes({"required":false, "id":"uploadedFileCount"})
     * @Form\Type("Hidden")
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "uploadNowRadio",
     *          "context_values": {"1"},
     *          "validators": {
     *              {
     *                  "name": "\Common\Validator\FileUploadCount",
     *                  "options": {
     *                      "min": 1,
     *                      "message": "lva-financial-evidence-upload.required"
     *                  }
     *              }
     *          }
     *      }
     * })
     */
    public $uploadedFileCount = null;
}
