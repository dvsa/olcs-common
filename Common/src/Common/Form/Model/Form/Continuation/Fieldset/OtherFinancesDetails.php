<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Class OtherAvailableBalanceDetails
 */
class OtherFinancesDetails
{
    /**
     * @Form\Type("Text")
     * @Form\Attributes({"id":"otherFinances_amount"})
     * @Form\Required(true)
     * @Form\Options({
     *     "label": "continuations.finances.otherFinances.amount.label",
     *     "hint": "continuations.finances.otherFinances.amount.hint",
     * })
     * @Form\Validator({"name": "NotEmpty", "options": {"null"}})
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "yesNo",
     *          "context_values": {"Y"},
     *          "inject_post_data": "finances->otherFinances->yesNo",
     *          "validators": {
     *              {
     *                  "name": "NotEmpty",
     *                  "options": {
     *                      "messages": {"isEmpty" : "continuations.finances.otherFinances.amount.empty"},
     *                      "break_chain_on_failure": true,
     *                  }
     *              },
     *              {
     *                  "name": "Dvsa\Olcs\Transfer\Validators\Money",
     *                  "options": {
     *                      "messages": {"invalid": "continuations.finances.otherFinances.amount.notNumber"},
     *                      "break_chain_on_failure": true,
     *                  }
     *              },
     *              {
     *                  "name": "Between",
     *                  "options": {
     *                      "min" : 0,
     *                      "max" : 99999999,
     *                      "messages": {"notBetween": "continuations.finances.otherFinances.amount.notBetween"}
     *                  }
     *              },
     *          }
     *      }
     * })
     */
    public $amount = null;

    /**
     * @Form\Type("Textarea")
     * @Form\Attributes({"id":"otherFinances_detail"})
     * @Form\Required(true)
     * @Form\Options({
     *     "label": "continuations.finances.otherFinances.detail.label",
     * })
     * @Form\Validator({"name": "NotEmpty", "options": {"null"}})
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "yesNo",
     *          "context_values": {"Y"},
     *          "inject_post_data": "finances->otherFinances->yesNo",
     *          "validators": {
     *              {
     *                  "name": "NotEmpty",
     *                  "options": {
     *                      "messages": {"isEmpty" : "continuations.finances.otherFinances.detail.empty"},
     *                      "break_chain_on_failure": true,
     *                  }
     *              },
     *              {
     *                  "name": "StringLength",
     *                  "options": {
     *                      "min" : 1,
     *                      "max" : 200,
     *                  }
     *              },
     *          }
     *      }
     * })
     */
    public $detail = null;
}
