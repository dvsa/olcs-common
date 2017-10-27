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
     * @Form\Options({"label": "Continue to financial history"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $continueToFinancialHistory = null;

    /**
     * @Form\Attributes({"type":"reset","class":"action--cancel", "id": "cancel"})
     * @Form\Options({"label": "Cancel"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionLink")
     */
    public $cancel = null;
}
