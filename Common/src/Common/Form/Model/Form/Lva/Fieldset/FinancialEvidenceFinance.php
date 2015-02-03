<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("finance")
 */
class FinancialEvidenceFinance
{
    /**
     * @Form\Attributes({"value": "markup-required-finance" })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $requiredFinance = null;
}
