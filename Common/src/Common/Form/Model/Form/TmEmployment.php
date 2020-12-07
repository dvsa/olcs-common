<?php

namespace Common\Form\Model\Form;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("employment")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 * @Form\Options({"prefer_form_input_filter": true, "action_lcfirst": true})
 */
class TmEmployment
{
    /**
     * @Form\Name("tm-employer-name-details")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TransportManager\EmployerNameDetails")
     */
    public $tmEmployerNameDetails = null;

    /**
     * @Form\Name("address")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     * @Form\Options({"label":"transport-manager.employment.form.address"})
     */
    public $address = null;

    /**
     * @Form\Name("tm-employment-details")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TransportManager\EmploymentDetails")
     */
    public $tmEmploymentDetails = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormCrudActions")
     */
    public $formActions = null;
}
