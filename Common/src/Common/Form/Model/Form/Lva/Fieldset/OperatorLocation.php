<?php

/**
 * Operator Location fieldset
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("operator-location")
 */
class OperatorLocation
{
    /**
    * @Form\Name("id")
    * @Form\Attributes({"method":"post"})
    * @Form\Type("\Zend\Form\Element\Hidden")
    */
    public $id = null;

    /**
    * @Form\Name("version")
    * @Form\Attributes({"method":"post"})
    * @Form\Type("\Zend\Form\Element\Hidden")
    */
    public $version = null;

    /**
    * @Form\Name("niFlag")
    * @Form\Options({"label":"application_type-of-licence_operator-location.data.niFlag"})
    * @Form\Attributes({"method":"post"})
    * @Form\Type("\Zend\Form\Element\Radio")
    */
    public $niFlag = null;
}
