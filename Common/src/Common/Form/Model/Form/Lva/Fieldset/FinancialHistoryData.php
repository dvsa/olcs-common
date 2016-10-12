<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
 */
class FinancialHistoryData
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"value":"markup-application_previous-history_financial-history-finance-hint"})
     * @Form\Type("Common\Form\Elements\Types\HtmlTranslated")
     */
    public $hasAnyPerson = null;

    /**
     * @Form\Annotations({"id":""})
     * @Form\Options({
     *     "short-label": "short-label-financial-history-bankrupt",
     *     "label": "application_previous-history_financial-history.finance.bankrupt",
     *     "error-message": "financialHistoryData_bankrupt-error",
     *     "value_options": {
     *         {
     *             "value": "Y",
     *             "label": "Yes",
     *             "label_attributes": {
     *                 "aria-label": "Has anyone you've ever named ever been decalred bankrupt or had their estate seized? Yes"
     *             }
     *         },
     *         {
     *             "value": "N",
     *             "label": "No"
     *         }
     *     },
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     },
     *     "fieldset-attributes" : {
     *         "id":"bankrupt",
     *         "class":"checkbox inline"
     *     }
     * })
     * @Form\Type("radio")
     */
    public $bankrupt = null;

    /**
     * @Form\Annotations({"id":""})
     * @Form\Options({
     *     "short-label": "short-label-financial-history-liquidation",
     *     "label": "application_previous-history_financial-history.finance.liquidation",
     *     "error-message": "financialHistoryData_liquidation-error",
     *     "value_options": {
     *         {
     *             "value": "Y",
     *             "label": "Yes",
     *             "label_attributes": {
     *                 "aria-label": "Has anyone you've named ever been involved with a business that has gone or is going into liquidation, owing money? Yes"
     *             }
     *         },
     *         {
     *             "value": "N",
     *             "label": "No"
     *         }
     *     },
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     },
     *     "fieldset-attributes" : {
     *         "id":"liquidation",
     *         "class":"checkbox inline"
     *     }
     * })
     * @Form\Type("radio")
     */
    public $liquidation = null;

    /**
     * @Form\Annotations({"id":""})
     * @Form\Options({
     *     "short-label": "short-label-financial-history-receivership",
     *     "label": "application_previous-history_financial-history.finance.receivership",
     *     "error-message": "financialHistoryData_receivership-error",
     *     "value_options": {
     *         {
     *             "value": "Y",
     *             "label": "Yes",
     *             "label_attributes": {
     *                 "aria-label": "Has anyone you've named ever been involved with a business that has gone or is going into receivership? Yes"
     *             }
     *         },
     *         {
     *             "value": "N",
     *             "label": "No"
     *         }
     *     },
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     },
     *     "fieldset-attributes" : {
     *         "id": "receiversip",
     *         "class":"checkbox inline"
     *     }
     * })
     * @Form\Type("radio")
     */
    public $receivership = null;

    /**
     * @Form\Annotations({"id":""})
     * @Form\Options({
     *     "short-label": "short-label-financial-history-administration",
     *     "label": "application_previous-history_financial-history.finance.administration",
     *     "error-message": "financialHistoryData_administration-error",
     *     "value_options": {
     *         {
     *             "value": "Y",
     *             "label": "Yes",
     *             "label_attributes": {
     *                 "aria-label": "Has anyone you've named ever been involved with a business that has gone or is going into administration? Yes"
     *             }
     *         },
     *         {
     *             "value": "N",
     *             "label": "No"
     *         }
     *     },
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     },
     *     "fieldset-attributes" : {
     *         "id":"administration",
     *         "class":"checkbox inline"
     *     }
     * })
     * @Form\Type("radio")
     */
    public $administration = null;

    /**
     * @Form\Annotations({"id":""})
     * @Form\Options({
     *     "short-label": "short-label-financial-history-disqualified",
     *      "fieldset-attributes": {
     *         "id":"disqualified",
     *         "class": "question checkbox inline"
     *      },
     *     "label": "application_previous-history_financial-history.finance.disqualified",
     *     "error-message": "financialHistoryData_disqualified-error",
     *     "value_options": {
     *         {
     *             "value": "Y",
     *             "label": "Yes",
     *             "label_attributes": {
     *                 "aria-label": "Has anyone you've named ever been disqualified from acting as a company director or managing a company under the Company Directors Disqualification Act 1986? Yes",
     *                 "class" : "inline"
     *             }
     *         },
     *         {
     *             "value": "N",
     *             "label": "No",
     *             "label_attributes": {
     *                 "class" : "inline"
     *             }
     *         }
     *     },
     * })
     * @Form\Type("radio")
     */
    public $disqualified = null;

    /**
     * @Form\Attributes({
     *     "value":"markup-application_previous-history_financial-history-insolvencyDetails-hint"
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $additionalInfoLabel = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Required(true)
     * @Form\Attributes({
     *     "required": false,
     *     "id": "",
     *     "class": "long js-financial-history",
     *     "placeholder": "application_previous-history_financial-history.insolvencyDetails.placeholder",
     *     "x-js-hint-chars-count": "application_previous-history_financial-history.insolvencyDetails.count-hint",
     * })
     * @Form\Options({
     *     "short-label": "short-label-financial-history-additional-information",
     *     "label": "short-label-financial-history-additional-information",
     *     "error-message": "financialHistoryData_insolvencyDetails-error",
     *     "label_attributes": {
     *         "class": "visually-hidden",
     *         "id": "additional-information"
     *     }
     * })
     * @Form\Type("TextArea")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name": "Common\Form\Elements\Validators\FHAdditionalInfo"})
     */
    public $insolvencyDetails = null;

    /**
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\MultipleFileUpload")
     * @Form\Attributes({"id":"file"})
     * @Form\Options({
     *     "label_attributes": {
     *         "aria-label": "insolvency"
     *     }
     * })
     */
    public $file = null;

    /**
     * @Form\Attributes({
     *     "id":"",
     *     "data-container-class": "confirm"
     * })
     * @Form\Options({
     *     "short-label": "short-label-financial-history-insolvency",
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "label": "application_previous-history_financial-history.insolvencyConfirmation.title",
     *     "help-block": "Please choose",
     *     "must_be_value": "Y",
     *     "label_attributes": {
     *         "id":"insolvency"
     *     }
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $insolvencyConfirmation = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $niFlag = null;
}
