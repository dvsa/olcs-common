<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-convictions-penalties")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ConvictionsPenalties
{
    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\ConvictionsPenaltiesData")
     */
    public $data = null;

    /**
     * @Form\Name("convictionsConfirmation")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\ConvictionsPenaltiesConfirmation")
     */
    public $convictionsConfirmation = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     */
    public $formActions = null;
}
