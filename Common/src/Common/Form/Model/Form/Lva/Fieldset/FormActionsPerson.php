<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("form-actions")
 * @Form\Attributes({"class":"actions-container"})
 */
class FormActionsPerson
{

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label": "save.continue.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $saveAndContinue = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--secondary large"})
     * @Form\Options({"label": "save.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $save = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--secondary large js-modal-ajax", "id": "disqualify"})
     * @Form\Options({
     *     "label": "Disqualify person",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-10",
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionLink")
     */
    public $disqualify = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--tertiary large"})
     * @Form\Options({"label": "cancel.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $cancel = null;
}
