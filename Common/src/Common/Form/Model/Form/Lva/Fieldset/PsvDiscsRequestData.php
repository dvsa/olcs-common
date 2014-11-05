<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("lva-psv-discs-request-data")
 */
class PsvDiscsRequestData
{
    /**
     * @Form\Name("totalAuth")
     * @Form\Type("hidden")
     */
    public $totalAuth = null;

    /**
     * @Form\Name("discCount")
     * @Form\Type("hidden")
     */
    public $discCount = null;

    /**
     * @Form\Name("additionalDiscs")
     * @Form\Type("text")
     * @Form\AllowEmpty(false)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Options({
     *     "label": "application_vehicle-safety_discs-psv-sub-action.additionalDiscs"
     * })
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "GreaterThan", "options": {"min":0}})
     * @Form\Validator({"name": "Common\Form\Elements\Validators\AdditionalPsvDiscsValidator"})
     */
    public $additionalDiscs = null;
}
