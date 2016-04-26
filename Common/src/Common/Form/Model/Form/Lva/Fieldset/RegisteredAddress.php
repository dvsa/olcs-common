<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("registered-address")
 * @Form\Type("\Zend\Form\Fieldset")
 * @Form\Options({"label":"Registered address"})
 * @Form\Attributes({
 *      "class": "address",
 * })
 */
class RegisteredAddress
{
    /**
     * @Form\Attributes({
     *   "value":""
     * })
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
     * @Form\Options({
     *     "label":"Address lines",
     *     "label_attributes": {
     *         "aria-label": "Enter address manually. Business address line one"
     *     }
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $addressLine1 = null;

    /**
     * @Form\Attributes({
     *   "class" : "long", 
     *   "id" : "",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({"label":"Address line 2","label_attributes":{"class":"visually-hidden"}})
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
     * @Form\Options({"label":"Address line 3","label_attributes":{"class":"visually-hidden"}})
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
     * @Form\Options({"label":"Address line 4","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $addressLine4 = null;

    /**
     * @Form\Attributes({
     *   "class" : "long", 
     *   "id" : ""
     * })
     * @Form\Options({
     *    "label":"Town/city",
     *    "label_attributes":{
     *        "class":"visually-hidden",
     *        "aria-label": "Business town or city"
     *    }
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $town = null;

    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *    "label":"Postcode",
     *    "label_attributes": {
     *        "aria-label": "Business Postcode"
     *    }
     * })
     * @Form\Type("Text")
     * @Form\Required(false)
     * @Form\Filter({"name":"Dvsa\Olcs\Transfer\Filter\Postcode"})
     * @Form\Validator({"name":"Dvsa\Olcs\Transfer\Validators\Postcode"});
     */
    public $postcode = null;
}
