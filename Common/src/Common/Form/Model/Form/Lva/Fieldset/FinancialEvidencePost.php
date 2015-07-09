<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("sendByPost")
 */
class FinancialEvidencePost
{
    /**
     * @Form\Attributes({
     *     "id":"sendByPost",
     *     "data-container-class": "send-by-post",
     *     "value": "markup-financial-evidence-send-by-post",
     *     "class": "send-by-post"
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $message = null;
}
