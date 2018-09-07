<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-transport-manager-details")
 * @Form\Attributes({
 *     "method":"post",
 *     "autocomplete": "off",
 * })
 * @Form\Type("Common\Form\Form")
 */
class TransportManagerDetails
{
    /**
     * @Form\Name("details")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TransportManager\Details")
     */
    public $details = null;

    /**
     * @Form\Name("homeAddress")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TransportManager\Address")
     * @Form\Options({
     *     "label":"lva-tm-details-details-homeAddress",
     *     "label_attributes": {
     *         "aria-label": "Postcode search, enter home postcode",
     *         "id":"homeAddress"
     *     }
     * })
     */
    public $homeAddress = null;

    /**
     * @Form\Name("workAddress")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TransportManager\Address")
     * @Form\Options({
     *     "label":"lva-tm-details-details-workAddress"
     * })
     */
    public $workAddress = null;

    /**
     * @Form\Name("responsibilities")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TransportManager\Responsibilities")
     * @Form\Options({"label":"lva-tm-details-details-responsibilities"})
     */
    public $responsibilities = null;

    /**
     * @Form\Name("otherEmployment")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({"id":"otherEmployment","data-section":"otherEmployment"})
     */
    public $otherEmployment = null;

    /**
     * @Form\Name("previousHistory")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TransportManager\PreviousHistory")
     */
    public $previousHistory = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TmDetailsFormActions")
     * @Form\Attributes({"class":"actions-container","data-section":"actions-container"})
     */
    public $formActions = null;
}
