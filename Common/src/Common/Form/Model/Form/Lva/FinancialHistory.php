<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-financial-history")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class FinancialHistory
{
    /**
     * @Form\Name("data")
     * @Form\Options({
     *     "hint": "application_previous-history_financial-history.finance.hint"
     * })
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FinancialHistoryData")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     */
    public $formActions = null;
}
