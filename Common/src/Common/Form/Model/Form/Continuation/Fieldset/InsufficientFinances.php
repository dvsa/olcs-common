<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Type("Common\Form\Elements\Types\RadioHorizontal")
 * @Form\Options({"label" : "continuations.insufficient-finances.label"})
 */
class InsufficientFinances
{
    /**
     * @Form\Type("Common\Form\Elements\Types\RadioYesNo")
     * @Form\ErrorMessage("continuations.insufficient-finances.error")
     * @Form\Validator({"name": "InArray", "options": {"haystack": {"Y"}}})
     */
    public $yesNo = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\FinancialEvidenceRequired")
     */
    public $yesContent = null;

    /**
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     * @Form\Attributes({"value": "markup-continuation-insufficient-finances"})
     */
    public $noContent = null;
}
