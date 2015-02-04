<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("finance")
 */
class FinancialEvidenceFinance
{
    /**
     * @Form\Attributes({"value": "markup-required-finance-application" })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     *
     * @todo different markup for each LVA
     */
    public $requiredFinance = null;
}
