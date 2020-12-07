<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("registered-address")
 * @Form\Type("\Laminas\Form\Fieldset")
 * @Form\Options({"label":"Registered address"})
 * @Form\Attributes({
 *      "class": "address",
 * })
 */
class OperatorRegisteredAddress
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
     * @Form\Attributes({
     *   "class" : "long",
     *   "id" : "",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({"label":"address_addressLines"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Laminas\Filter\StringTrim"})
     */
    public $addressLine1 = null;

    /**
     * @Form\Attributes({
     *   "class" : "long",
     *   "id" : "",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({"label":"address_addressLine2","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $addressLine2 = null;

    /**
     * @Form\Attributes({
     *   "class" : "long",
     *   "id" : "",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({"label":"address_addressLine3","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $addressLine3 = null;

    /**
     * @Form\Attributes({
     *   "class" : "long",
     *   "id" : "",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({"label":"address_addressLine4","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $addressLine4 = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"address_townCity","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $town = null;

    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({"label":"address_postcode"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Dvsa\Olcs\Transfer\Filter\Postcode"})
     * @Form\Validator({"name":"Dvsa\Olcs\Transfer\Validators\Postcode"});
     */
    public $postcode = null;
}
