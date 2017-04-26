<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("uploadLater")
 */
class FinancialEvidenceUploadLater
{
    /**
     * @Form\Attributes({
     *     "id":"sendByPost",
     *     "data-container-class": "upload-later",
     *     "value": "markup-financial-evidence-upload-later",
     *     "class": "send-by-post"
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $message = null;
}
