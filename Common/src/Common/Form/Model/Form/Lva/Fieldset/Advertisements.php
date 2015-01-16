<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("advertisements")
 * @Form\Options({
 *     "label":
 * "application_operating-centres_authorisation-sub-action.advertisements"
 * })
 */
class Advertisements
{
    /**
     * @Form\Attributes({"id":"adPlaced","placeholder":""})
     * @Form\Options({
     *      "fieldset-attributes": {
     *          "class": "checkbox inline"
     *      },
     *     "label":
     * "application_operating-centres_authorisation-sub-action.advertisements.adPlaced",
     *     "value_options": {"Y":"Yes", "N":"No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $adPlaced = null;

    /**
     * @Form\Required(true)
     * @Form\Attributes({"class":"","id":"adPlacedIn","required":false})
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation-sub-action.advertisements.adPlacedIn"
     * })
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "adPlaced",
     *          "context_values": {"Y"},
     *          "validators": {
     *              {"name": "\Zend\Validator\NotEmpty"}
     *          }
     *      }
     * })
     * @Form\Type("Text")
     */
    public $adPlacedIn = null;

    /**
     * @Form\Required(true)
     * @Form\Attributes({"id":"adPlacedDate","required":false})
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation-sub-action.advertisements.adPlacedDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\AllowEmpty(true)
     * @Form\Filter({"name": "DateSelectNullifier"})
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "adPlaced",
     *          "context_values": {"Y"},
     *          "validators": {
     *              {
     *                  "name": "Common\Form\Elements\Validators\Date", "options":
     *                  {
     *                      "format": "Y-m-d"
     *                  }
     *              }
     *          }
     *      }
     * })
     * @Form\Type("DateSelect")
     */
    public $adPlacedDate = null;

    /**
     * @Form\Attributes({"id":"file", "class": "file-upload"})
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation-sub-action.advertisements.file",
     *     "hint":
     * "application_operating-centres_authorisation-sub-action.advertisements.file.hint"
     * })
     * @Form\Type("\Common\Form\Elements\Types\MultipleFileUpload")
     */
    public $file = null;
}
