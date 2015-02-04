<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("finance")
 */
class FinancialEvidenceFinance
{
    /**
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     *
     * (value is set by individual LVA adapters)
     */
    public $requiredFinance = null;
}
