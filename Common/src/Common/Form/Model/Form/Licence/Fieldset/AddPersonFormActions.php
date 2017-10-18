<?php

namespace Common\Form\Model\Form\Licence\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Class AddPersonFormActions
 *
 * @package Common\Form\Model\Form\Licence\Fieldset
 */
class AddPersonFormActions
{

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label": "continue.finance.history.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $continueToFinancialHistory = null;
}
