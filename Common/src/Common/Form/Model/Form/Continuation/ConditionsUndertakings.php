<?php

namespace Common\Form\Model\Form\Continuation;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ConditionsUndertakings
{

    /**
     * @Form\Attributes({"id":"confirmation"})
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "label": "continuations.conditions-undertakings.confirmation",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--advanced"},
     *     "must_be_value": "Y",
     *     "not_checked_message":"continuations.conditions-undertakings.confirmation.error"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $confirmation = null;

    /**
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     * @Form\Attributes({
     *     "value": "continuations.conditions-undertakings.summary",
     * })
     */
    public $summary = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label":"continuations.conditions-undertakings.continue.label"})
     * @Form\Type("\Laminas\Form\Element\Button")
     */
    public $submit = null;
}
