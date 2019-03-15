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
     * @Form\Options({
     *     "label":"Postcode search", "label_attributes": {"class": "form-element__label"}
     * })
     * @Form\Required(false)
     * @Form\Type("Common\Form\Elements\Types\PostcodeSearch")
     */
    public $searchPostcode = null;

    /**
     * @Form\Attributes({
     *   "class" : "long", 
     *   "id" : "addressLine1",
     *   "data-container-class" : "compound"
     * })
     * @Form\Options({
     *     "label":"address_addressLines",
     *     "error-message" : "address_addressLine1-error", 
     *     "label_attributes": {
     *         "aria-label": "Enter address manually. Address line one"
     *     },
     *     "short-label":"address_addressLine1"
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
     * @Form\Options({"label":"address_addressLine2","label_attributes":{"class":"visually-hidden"}})
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
     * @Form\Options({"label":"address_addressLine3","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":100}})
     */
    public $addressLine3 = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"address_addressLine4","label_attributes":{"class":"visually-hidden"}})
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":35}})
     */
    public $addressLine4 = null;

    /**
     * @Form\Attributes({"class":"long","id":"addressTown"})
     * @Form\Options({
     *     "label":"address_townCity",
     *     "short-label":"address_townCity",
     *     "label_attributes": {
     *         "aria-label": "address_townCity"
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
     *     "label":"address_postcode",
     *     "short-label":"address_postcode",
     *     "error-message" : "address_postcode-error",
     * })
     *
     * @Form\Type("Text")
     * @Form\Required(true)
     * @Form\Attributes({"id":"postcode", "required":false})
     * @Form\Validator({"name":"Zend\Validator\NotEmpty","options":{"null"}})
     * @Form\Validator({"name":"Dvsa\Olcs\Transfer\Validators\Postcode"})
     * @Flags({"priority": 1000})
     */
    public $postcode = null;

    /**
     * @Form\Attributes({"id":"","placeholder":"","value":"GB"})
     * @Form\Options({
     *     "label": "address_country",
     *     "label_attributes": {
     *         "aria-label": "Choose country"
     *     },
     *     "error-message" : "address_country-error",
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "service_name": "Common\Service\Data\Country"
     * })
     * @Form\Type("DynamicSelect")
     * @Flags({"priority": 2})
     */
    public $countryCode = null;
}
