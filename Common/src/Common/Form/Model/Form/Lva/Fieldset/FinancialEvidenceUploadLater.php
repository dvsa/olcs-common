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
     *     "id":"uploadLaterMessage",
     *     "data-container-class": "upload-later",
     *     "value": "markup-financial-evidence-upload-later",
     *     "class": "upload-later-message"
     * })
     * @Form\Options({
     *     "fieldset-attributes": {"class":"uploadLaterFieldset"}
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     * @Form\Name("uploadLaterMessage")
     */
    public $message = null;
}
