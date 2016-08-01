<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("address")
 * @Form\Type("\Common\Form\Elements\Types\Address")
 * @Form\Attributes({
 *     "class": "address js-postcode-search"
 * })
 */
class Address
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
     * @Form\Options({"label":"Postcode search"})
     * @Form\Required(false)
     * @Form\Type("Common\Form\Elements\Types\PostcodeSearch")
     */
    public $searchPostcode = null;

    /**
     * @Form\Attributes({
     *   "class" : "long", 
     *   "id" : "",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({
     *     "label":"Address lines",
     *     "error-message" : "address_addressLine1-error", 
     *     "label_attributes": {
     *         "aria-label": "Enter address manually. Address line one"
     *     },
     *     "short-label":"Address line 1"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":90}})
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
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":90}})
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
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":100}})
     */
    public $addressLine3 = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"Address line 4","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":35}})
     */
    public $addressLine4 = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":"Town/city",
     *     "short-label":"Town/city",
     *     "label_attributes": {
     *         "aria-label": "Town/city"
     *     },
     *     "error-message" : "address_town-error",
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":30}})
     */
    public $town = null;

    /**
     * @Form\Options({
     *     "label":"Postcode",
     *     "short-label":"Postcode",
     *     "error-message" : "address_postcode-error",
     * })
     * @Form\Type("Text")
     * @Form\AllowEmpty(true)
     * @Form\Required(true)
     * @Form\Attributes({"id":"", "required":false})
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Validator({"name":"Dvsa\Olcs\Transfer\Validators\Postcode"});
     */
    public $postcode = null;

    /**
     * @Form\Attributes({"id":"","placeholder":"","value":"GB"})
     * @Form\Options({
     *     "label": "Country",
     *     "label_attributes": {
     *         "aria-label": "Choose country"
     *     },
     *     "error-message" : "address_country-error",
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "help-block": "Please select a category",
     *     "service_name": "Common\Service\Data\Country"
     * })
     * @Form\Type("DynamicSelect")
     */
    public $countryCode = null;
}
