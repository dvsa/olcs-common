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
     *     "value_options": {"Y": "Yes", "N": "No"},
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
     *     "value_options": {"Y": "Yes", "N": "No"},
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
     *     "value_options": {"Y": "Yes", "N": "No"},
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
     *     "value_options": {"Y": "Yes", "N": "No"},
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
     *     "value_options": {"Y": "Yes", "N": "No"}
     * })
     * @Form\Type("radio")
     */
    public $disqualified = null;

    /**
     * @Form\Attributes({
     *     "value":"markup-application_previous-history_financial-history-insolvencyDetails-hint",
     *     "data-container-class":"legend"
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
     *     "placeholder": "application_previous-history_financial-history.insolvencyDetails.placeholder"
     * })
     * @Form\Options({
     *     "short-label": "short-label-financial-history-additional-information",
     *     "label": "",
     *     "label_attributes": {
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
