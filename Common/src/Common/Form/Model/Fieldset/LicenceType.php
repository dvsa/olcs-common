<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("licence-type")
 * @Form\Attributes({"class":"hidden"})
 * @Form\Options({"label":"application_type-of-licence_licence-type.data"})
 */
class LicenceType
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_type-of-licence_licence-type.data.licenceType",
     *     "help-block": "Please choose",
     *     "service_name": "staticList",
     *     "category": "licence_types"
     * })
     * @Form\Type("DynamicRadio")
     */
    public $licenceType = null;
}
