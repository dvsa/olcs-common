<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Type("\Common\Form\Elements\Types\RadioHorizontal")
 * @Form\Options({"label" : "continuations.finances.overdraftFacility.label"})
 */
class OverdraftFacility
{
    /**
     * @Form\Type("Common\Form\Elements\Types\RadioYesNo")
     * @Form\Options({
     *     "label": "continuations.finances.overdraftFacility.label",
     * })
     * @Form\ErrorMessage("continuations.finances.overdraftFacility.error")
     */
    public $yesNo = null;

    /**
     * @Form\Type("Text")
     * @Form\Attributes({"id":"overDraftLimit"})
     * @Form\Required(true)
     * @Form\Options({
     *     "label": "continuations.finances.overdraftFacility.amount.label",
     * })
     * @Form\Validator({"name": "Zend\Validator\NotEmpty", "options": {"null"}})
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "yesNo",
     *          "context_values": {"Y"},
     *          "validators": {
     *              {
     *                  "name": "NotEmpty",
     *                  "options": {
     *                      "messages": {"isEmpty" : "continuations.finances.overdraftFacility.amount.empty"},
     *                      "break_chain_on_failure": true,
     *                  }
     *              },
     *              {
     *                  "name": "Dvsa\Olcs\Transfer\Validators\Money",
     *                  "options": {
     *                      "messages": {"invalid": "continuations.finances.overdraftFacility.amount.notNumber"},
     *                      "break_chain_on_failure": true,
     *                  }
     *              },
     *              {
     *                  "name": "Between",
     *                  "options": {
     *                      "min" : 0,
     *                      "max" : 99999999,
     *                      "messages": {"notBetween": "continuations.finances.overdraftFacility.amount.notBetween"},
     *                  }
     *              },
     *          }
     *      }
     * })
     */
    public $yesContent = null;
}
