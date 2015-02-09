<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("upload")
 */
class FinancialEvidenceUpload
{
    /**
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Required(true)
     * @Form\Attributes({"id":"file", "class": "file-upload", "required":false})
     * @Form\Options({
     *     "label" : "",
     *     "hint": "lva-financial-evidence-upload.files.hint"
     * })
     * @Form\Type("\Common\Form\Elements\Types\MultipleFileUpload")
     */
    public $file = null;
}
