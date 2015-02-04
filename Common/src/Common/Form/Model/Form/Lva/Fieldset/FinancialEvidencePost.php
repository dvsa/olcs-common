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
     *     "value": "markup-financial-evidence-send-by-post"
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $message = null;
}
