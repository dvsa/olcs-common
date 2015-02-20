<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-community-licences-stop")
 * @Form\Attributes({"method":"post", "class":"table__form"})
 * @Form\Type("Common\Form\Form")
 */
class CommunityLicencesStop
{
    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\CommunityLicencesDataStop")
     */
    public $data = null;

    /**
     * @Form\Attributes({"id":"dates"})
     * @Form\Name("dates")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\CommunityLicencesDataStopDates")
     */
    public $dates = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OkCancelActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
