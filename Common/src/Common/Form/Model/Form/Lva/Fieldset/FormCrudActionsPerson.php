<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("form-actions")
 * @Form\Attributes({"class":"actions-container"})
 */
class FormCrudActionsPerson
{
    /**
     * @Form\Attributes({
     *     "type":"submit",
     *     "class":"action--primary large",
     *     "aria-label": "Save and continue"
     * })
     * @Form\Options({
     *     "label": "Save"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $submit = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--secondary large", "id": "cancel"})
     * @Form\Options({"label": "cancel.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $cancel = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--secondary large js-modal-ajax", "id": "disqualify"})
     * @Form\Options({"label": "Disqualify person"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionLink")
     */
    public $disqualify = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--tertiary large"})
     * @Form\Options({"label": "Save and add another"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $addAnother = null;
}
