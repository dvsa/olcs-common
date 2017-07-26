<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Class Finances
 */
class Finances
{
    /**
     * @Form\Type("hidden")
     */
    public $version = null;

    /**
     * @Form\Type("Text")
     * @Form\Attributes({"id":"averageBalance"})
     * @Form\Options({
     *     "label":"continuations.finances.averageBalance.label",
     *     "hint":"continuations.finances.averageBalance.hint",
     *     "label_attributes": {"class": "form-element__question"},
     *     "hint-below": "markup-continuation-finances-average-balance",
     * })
     * @Form\Validator({"name":"NotEmpty", "options": {
     *     "messages": {"isEmpty" : "continuations.finances.averageBalance.empty"},
     *     "break_chain_on_failure": true,
     * }})
     * @Form\Validator({"name":"Dvsa\Olcs\Transfer\Validators\Money", "options": {
     *     "allow_negative" : true,
     *     "messages": {
     *          "invalid": "continuations.finances.averageBalance.notNumber"
     *     }
     * }})
     * @Form\Validator({"name":"Between", "options": {
     *     "min": -99999999,
     *     "max": 99999999,
     *     "messages": {
     *         "notBetween": "continuations.finances.averageBalance.notBetween"
     *     }
     * }})
     */
    public $averageBalance = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\OverdraftFacility")
     */
    public $overdraftFacility = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\FinancesFactoring")
     * @Form\Options({"label": "continuations.finances.factoring.label"})
     */
    public $factoring = null;
}
