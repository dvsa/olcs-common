<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Type("\Common\Form\Elements\Types\RadioVertical")
 * @Form\Options({
 *     "label" : "continuations.financial-evidence-required.label",
 *     "hint" : "markup-continuation-financial-evidence-required-hint",
 * })
 */
class FinancialEvidenceRequired
{
    /**
     * @Form\Type("Common\Form\Elements\Types\Radio")
     * @Form\Options({
     *     "value_options": {
     *          "upload": "lva-financial-evidence-upload-now.yes",
     *          "send": "lva-financial-evidence-upload-now.no",
     *      },
     * })
     * @Form\ErrorMessage("continuations.financial-evidence-required.error")
     */
    public $radio = null;

    /**
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\MultipleFileUpload")
     */
    public $uploadContent = null;

    /**
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     * @Form\Attributes({
     *     "value": "markup-continuation-insufficient-finances-send-by-post",
     * })
     */
    public $sendContent = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label":"Continue"})
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $submit = null;
}
