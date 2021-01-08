<?php

namespace Common\Form\Model\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("fields")
 * @Form\Options({})
 */
class Fields
{
    /**
     * @Form\Attributes({"id":"","class":"extra-long"})
     * @Form\Options({"label": "Case summary"})
     * @Form\Type("\Laminas\Form\Element\Textarea")
     * @Form\Filter({"name":"Laminas\Filter\StringTrim"})
     * @Form\Filter({"name":"Laminas\Filter\StringToLower"})
     * @Form\Validator({"name":"Laminas\Validator\StringLength","options":{"min":10,"max":100}})
     */
    public $description = null;

    /**
     * @Form\Attributes({"class":"medium","id":""})
     * @Form\Options({"label":"ECMS number"})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $ecmsNo = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $licence = null;

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
}
