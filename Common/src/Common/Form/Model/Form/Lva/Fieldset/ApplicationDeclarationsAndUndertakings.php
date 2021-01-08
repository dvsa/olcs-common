<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("declarations")
 */
class ApplicationDeclarationsAndUndertakings
{
    /**
     * @Form\Attributes({"value": "markup-review-text"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $review = null;

    /**
     * @Form\Attributes({"value": "markup-declaration-text"})
     * @Form\Type("Common\Form\Elements\Types\GuidanceTranslated")
     */
    public $declaration = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application.signature.options.label",
     *     "label_attributes": {
     *         "class":"form-control form-control--radio form-control--advanced"
     *     },
     *     "value_options": {"Y": "application.signature.options.verify", "N": "application.signature.options.sign"},
     *     "label_options": {
     *         "disable_html_escape": "true"
     *     }
     * })
     * @Form\Type("\Laminas\Form\Element\Radio")
     */
    public $signatureOptions = null;

    /**
     * @Form\Attributes({"value": "markup-signature-disabled-text"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $disabledReview = null;

    /**
     * @Form\Attributes({"id":"declarationDownload"})
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $declarationDownload = null;

    /**
     * @Form\Attributes({"value": "markup-declaration-for-verify","data-container-class":"declarationForVerify"})
     * @Form\Type("Common\Form\Elements\Types\GuidanceTranslated")
     */
    public $declarationForVerify = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;
}
