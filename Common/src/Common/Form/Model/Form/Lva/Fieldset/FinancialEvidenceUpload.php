<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("upload")
 */
class FinancialEvidenceUpload
{
    /**
     * @Form\Required(true)
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Attributes({"id":"file", "class": "file-upload", "required":false})
     * @Form\Options({
     *     "label" : "",
     *     "hint": "lva-financial-evidence-upload.files.hint"
     * })
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "uploadNow",
     *          "context_values": {"Y"},
     *          "validators": {
     *              {"name": "\Zend\Validator\NotEmpty"}
     *          }
     *      }
     * })
     * @Form\Type("\Common\Form\Elements\Types\MultipleFileUpload")
     */
    public $file = null;
}
