<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("form-actions")
 * @Form\Attributes({"class":"actions-container"})
 */
class VehiclesTransferActions
{

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label": "transfer.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $transfer = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--secondary large","id": "cancel"})
     * @Form\Options({"label": "cancel.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $cancel = null;
}
