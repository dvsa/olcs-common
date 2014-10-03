<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_previous-history_financial-history")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationPreviousHistoryFinancialHistory
{

    /**
     * @Form\Name("data")
     * @Form\Options({
     *     "label": "application_previous-history_financial-history.finance.title",
     *     "hint": "application_previous-history_financial-history.finance.hint"
     * })
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationPreviousHistoryFinancialHistoryData")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

