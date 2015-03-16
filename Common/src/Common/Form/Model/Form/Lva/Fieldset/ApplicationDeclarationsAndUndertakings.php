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
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $summaryDownload = null;

    /**
     * @Form\Attributes({"value": "<h3>%s</h3>" })
     * @Form\Options({"tokens": { 0: "section.name.undertakings" } })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $heading = null;

    /**
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $undertakings = null;

    /**
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "must_be_value": "Y",
     *     "label": "application.review-declarations.confirm-text",
     *     "short-label": "application.review-declarations.confirm-short-label",
     *     "label_attributes": {"id": "label-declarationConfirmation"}
     * })
     * @Form\Attributes({"data-container-class": "confirm"})
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
