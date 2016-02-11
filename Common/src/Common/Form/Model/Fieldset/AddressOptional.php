<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("address")
 * @Form\Type("\Common\Form\Elements\Types\Address")
 * @Form\Attributes({
 *     "class": "address js-postcode-search",
 *     "aria-live":"assertive"
 * })
 */
class AddressOptional
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
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":"Address lines",
     *     "short-label":"Address line 1"
     * })
     * @Form\Required(false)
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
     * @Form\Options({"label":"Town/city"})
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $town = null;

    /**
     * @Form\Options({"label":"Postcode"})
     * @Form\Type("Text")
     * @Form\AllowEmpty(true)
     * @Form\Required(true)
     * @Form\Attributes({"id":"", "required":false})
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Validator({
     *      "name":"Dvsa\Olcs\Transfer\Validators\Postcode",
     *      "options": {"allow_empty": true}
     * });
     */
    public $postcode = null;

    /**
     * @Form\Attributes({"id":"","placeholder":"","value":"GB"})
     * @Form\Required(false)
     * @Form\Options({
     *     "label": "Country",
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "help-block": "Please select a category",
     *     "service_name": "Common\Service\Data\Country"
     * })
     * @Form\Type("DynamicSelect")
     */
    public $countryCode = null;
}
