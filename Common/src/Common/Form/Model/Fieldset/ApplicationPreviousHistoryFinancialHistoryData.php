<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("data")
 * @Form\Options({
 *     "label": "application_previous-history_financial-history.finance.title",
 *     "hint": "application_previous-history_financial-history.finance.hint"
 * })
 */
class ApplicationPreviousHistoryFinancialHistoryData
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
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_previous-history_financial-history.finance.bankrupt",
     *     "value_options": "yes_no",
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $bankrupt = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_previous-history_financial-history.finance.liquidation",
     *     "value_options": "yes_no",
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $liquidation = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_previous-history_financial-history.finance.receivership",
     *     "value_options": "yes_no",
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $receivership = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_previous-history_financial-history.finance.administration",
     *     "value_options": "yes_no",
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $administration = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_previous-history_financial-history.finance.disqualified",
     *     "value_options": "yes_no",
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $disqualified = null;

    /**
     * @Form\Attributes({
     *     "id": "",
     *     "class": "long",
     *     "placeholder":
     * "application_previous-history_financial-history.insolvencyDetails.placeholder",
     *     "data-container-class": "highlight-box"
     * })
     * @Form\Options({
     *     "label":
     * "application_previous-history_financial-history.insolvencyDetails.title",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6",
     *     "help-block": "You can type anything in this box.",
     *     "hint":
     * "application_previous-history_financial-history.insolvencyDetails.hint"
     * })
     * @Form\Type("TextArea")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $insolvencyDetails = null;

    /**
     * @Form\Type("\Common\Form\Elements\Types\MultipleFileUpload")
     */
    public $file = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "label":
     * "application_previous-history_financial-history.insolvencyConfirmation.title",
     *     "help-block": "Please choose",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $insolvencyConfirmation = null;


}

