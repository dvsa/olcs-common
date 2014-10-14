<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("registered-address")
 * @Form\Type("\Zend\Form\Fieldset")
 */
class RegisteredAddress
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
     * @Form\Options({"label":"Address line 1","label_attributes":{"class":"visually-hidden"}})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $addressLine1 = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"Address line 2","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $addressLine2 = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"Address line 3","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $addressLine3 = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"Address line 4","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $addressLine4 = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"Town/City","label_attributes":{"class":"visually-hidden"}})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $town = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"Postcode"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $postcode = null;
}
