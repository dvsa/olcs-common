<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("advertisements")
 * @Form\Options({
 *     "label": "application_operating-centres_authorisation-sub-action.advertisements"
 * })
 */
class Advertisements
{
    /**
     * @Form\Attributes({"id":"adPlaced","placeholder":""})
     * @Form\Options({
     *     "fieldset-attributes": {
     *         "class": "checkbox"
     *     },
     *     "error-message": "advertisements_adPlaced-error",
     *     "label": "application_operating-centres_authorisation-sub-action.advertisements.adPlaced",
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
     * @Form\Required(false)
     * @Form\Attributes({"class":"long","id":"adPlacedIn"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.advertisements.adPlacedIn"
     * })
     * @Form\Type("Text")
     */
    public $adPlacedIn = null;

    /**
     * @Form\Required(false)
     * @Form\Attributes({"id":"adPlacedDate", "data-container-class": "adPlacedDate"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.advertisements.adPlacedDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false,
     *     "fieldset-attributes":{
     *          "id":"adPlacedDate_day"
     *      }
     * })
     * @Form\Filter({"name": "DateSelectNullifier"})
     * @Form\Type("DateSelect")
     * @Form\Validator({"name": "\Common\Validator\Date"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     */
    public $adPlacedDate = null;

    /**
     * @Form\Name("file")
     * @Form\Attributes({"id":"file"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\MultipleFileUpload")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.advertisements.file"
     * })
     */
    public $file = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Options({
     *     "fieldset-attributes": {
     *          "id": "advertisements[file][file]",
     *     },
     * })
     * @Form\Required(true)
     * @Form\Attributes({"required":false, "id":"uploadedFileCount"})
     * @Form\Type("Hidden")
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "adPlaced",
     *          "context_values": {"Y"},
     *          "validators": {
     *              {
     *                  "name": "\Common\Validator\FileUploadCount",
     *                  "options": {
     *                      "min": 1,
     *                      "message": "ERR_OC_AD_FI_1"
     *                  }
     *              }
     *          }
     *      }
     * })
     */
    public $uploadedFileCount = null;

    /**
     * @Form\Attributes({"data-container-class":"ad-send-by-post"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $adSendByPost = null;
}
