<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("evidence")
 */
class FinancialEvidenceEvidence
{
    /**
     * @Form\Options({
     *     "fieldset-attributes": {
     *          "class": "checkbox inline",
     *     },
     *     "label": "lva-financial-evidence-upload-now.label",
     *     "value_options": {
     *         "Y":"lva-financial-evidence-upload-now.yes",
     *         "N":"lva-financial-evidence-upload-now.no"
     *     },
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Attributes({"value": "Y"})
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $uploadNow = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Required(true)
     * @Form\Attributes({"id":"files", "class": "file-upload", "required":false})
     * @Form\Options({
     *     "label" : "",
     *     "hint": "lva-financial-evidence-upload.files.hint",
     *     "fieldset-attributes": {"label": "lva-financial-evidence-upload.label"}
     * })
     * @Form\Validator({
     *     "name": "Common\Form\Elements\Validators\EvidenceRequiredValidator",
     *     "options": {
     *         "label": "financial standing"
     *     }
     * })
     * @Form\Type("\Common\Form\Elements\Types\MultipleFileUpload")
     */
    public $files = null;
}
