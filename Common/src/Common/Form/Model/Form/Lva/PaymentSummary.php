<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-payment-summary")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class PaymentSummary
{
    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label": "summary-dashboard.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $submitDashboard = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary secondary"})
     * @Form\Options({"label": "summary-overview.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $submitOverview = null;
}
