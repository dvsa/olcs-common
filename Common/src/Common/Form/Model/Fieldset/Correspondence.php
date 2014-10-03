<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("correspondence")
 * @Form\Options({"label":"application_your-business_business-type.correspondence.label"})
 */
class Correspondence
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
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"application_your-business_fao.label"})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $fao = null;


}

