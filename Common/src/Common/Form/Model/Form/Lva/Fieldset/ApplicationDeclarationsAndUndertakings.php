<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

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
     *      "fieldset-attributes": {
     *          "class":"checkbox has-advanced-labels"
     *      },
     *     "label": "application.signature.options.label",
     *     "value_options": {"Y": "application.signature.options.verify", "N": "application.signature.options.sign"},
     *     "label_options": {
     *         "disable_html_escape": "true"
     *     },
     *     "help-block": "Please choose",
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
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
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "must_be_value": "Y",
     *     "label": "application.review-declarations.confirm-verify-text",
     *     "short-label": "application.review-declarations.confirm-short-label",
     *     "label_attributes": {"id": "label-declarationConfirmation"}
     * })
     * @Form\Attributes({"data-container-class": "confirm checkbox"})
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $declarationConfirmation = null;

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
