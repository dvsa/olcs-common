<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("licence_operating-centres_authorisation-add")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class LicenceOperatingCentresAuthorisationAdd
{
    /**
     * @Form\Name("data")
     * @Form\Options({"label":"Adding an operating centre"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\LicenceOperatingCentresAuthorisationAddData")
     */
    public $data = null;
}
