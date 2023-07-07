<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("declarations")
 */
class VariationDeclarationsAndUndertakings
{
    /**
     * @Form\Attributes({"value": "markup-review-text-variation"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $review = null;

    /**
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $summaryDownload = null;

    /**
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "Y",
     *     "must_be_value": "Y",
     *     "label": "variation.review-declarations.confirm-text",
     *     "short-label": "variation.review-declarations.confirm-short-label",
     *     "label_attributes": {
     *         "class": "form-control form-control--checkbox form-control--advanced",
     *         "id": "label-declarationConfirmation"
     *     }
     * })
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
