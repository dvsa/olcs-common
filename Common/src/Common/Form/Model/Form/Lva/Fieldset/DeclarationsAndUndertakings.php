<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("declarations")
 */
class DeclarationsAndUndertakings
{
    /**
     * @Form\Attributes({"value": "markup-review-text"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $review = null;

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
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $declarationConfirmation = null;

    /**
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $declarations = null;

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
