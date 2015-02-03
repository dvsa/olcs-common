<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("upload")
 */
class FinancialEvidenceUpload
{
    /**
     * @Form\Attributes({"id":"file", "class": "file-upload"})
     * @Form\Options({
     *     "label" : "",
     *     "hint": "lva-financial-evidence-upload.files.hint"
     * })
     * @Form\Type("\Common\Form\Elements\Types\MultipleFileUpload")
     */
    public $file = null;
}
