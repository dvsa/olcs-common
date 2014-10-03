<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_previous-history_convictions-penalties")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationPreviousHistoryConvictionsPenalties
{

    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationPreviousHistoryConvictionsPenaltiesData")
     */
    public $data = null;

    /**
     * @Form\Name("table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $table = null;

    /**
     * @Form\Name("convictionsConfirmation")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ConvictionsConfirmation")
     */
    public $convictionsConfirmation = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

