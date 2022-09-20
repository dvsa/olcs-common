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
     *   "id" : "addressLine1",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({
     *     "label":"address_addressLines",
     *     "label_attributes": {
     *         "aria-label": "Enter address manually. Business address line one"
     *     },
     *     "error-message" : "registeredAddress_addressLine1-error",
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Laminas\Filter\StringTrim"})
     * @Form\Validator({"name":"Laminas\Validator\StringLength", "options":{
     *     "min": 0, "max": 200
     * }})
     */
    public $addressLine1 = null;

    /**
     * @Form\Attributes({
     *   "class" : "long",
     *   "id" : "",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({
     *     "label":"address_addressLine2",
     *     "label_attributes":{"class":"govuk-visually-hidden"}
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Validator({"name":"Laminas\Validator\StringLength", "options":{
     *     "min": 0, "max": 200
     * }})
     */
    public $addressLine2 = null;

    /**
     * @Form\Attributes({
     *   "class" : "long",
     *   "id" : "",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({
     *     "label":"address_addressLine3",
     *     "label_attributes":{"class":"govuk-visually-hidden"}
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Validator({"name":"Laminas\Validator\StringLength", "options":{
     *     "min": 0, "max": 200
     * }})
     */
    public $addressLine3 = null;

    /**
     * @Form\Attributes({
     *   "class" : "long",
     *   "id" : "",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({
     *     "label":"address_addressLine4",
     *     "label_attributes":{"class":"govuk-visually-hidden"}
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Validator({"name":"Laminas\Validator\StringLength", "options":{
     *     "min": 0, "max": 200
     * }})
     */
    public $addressLine4 = null;

    /**
     * @Form\Attributes({
     *   "class" : "long",
     *   "id" : ""
     * })
     * @Form\Options({
     *    "label":"address_townCity",
     *    "label_attributes":{
     *        "class":"govuk-visually-hidden",
     *        "aria-label": "Business town or city"
     *    }
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Validator({"name":"Laminas\Validator\StringLength", "options":{
     *     "min": 0, "max": 200
     * }})
     */
    public $town = null;

    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *    "label":"address_postcode",
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
