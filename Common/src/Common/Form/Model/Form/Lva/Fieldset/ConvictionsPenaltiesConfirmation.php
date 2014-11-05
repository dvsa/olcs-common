<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("convictionsConfirmation")
 */
class ConvictionsPenaltiesConfirmation
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "label": "selfserve-app-subSection-previous-history-criminal-conviction-labelConfirm",
     *     "help-block": "Please choose",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $convictionsConfirmation = null;
}
