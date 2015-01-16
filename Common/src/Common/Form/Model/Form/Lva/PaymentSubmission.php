<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-payment-submission")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class PaymentSubmission
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({
     *     "value": "<div>Application fee <h2>&pound;%s</h2></div>"
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $amount = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label": "submit-pay.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $submitPay = null;
}
