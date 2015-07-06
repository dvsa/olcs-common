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
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $uploadNow = null;

    /**
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\MultipleFileUpload")
     * @Form\Attributes({"id":"files"})
     */
    public $files = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Required(true)
     * @Form\Attributes({"required":false, "id":"uploadedFileCount"})
     * @Form\Type("Hidden")
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "uploadNow",
     *          "context_values": {"Y"},
     *          "validators": {
     *              {
     *                  "name": "\Zend\Validator\GreaterThan",
     *                  "options": {
     *                      "min": 0,
     *                      "message": "lva-financial-evidence-upload.required"
     *                  }
     *              }
     *          }
     *      }
     * })
     */
    public $uploadedFileCount = null;
}
