<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Type("\Common\Form\Elements\Types\RadioVertical")
 * @Form\Name("advertisements")
 * @Form\Options({
 *     "label": "application_operating-centres_authorisation-sub-action.advertisements"
 * })
 */
class Advertisements
{
    /**
     * @Form\Type("Common\Form\Elements\Types\Radio")
     * @Form\Attributes({"id":"adPlacedPost","allowWrap":true,"data-container-class":"form-control__container"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.advertisements.adPlaced",
     *     "label_attributes": {"class": "form-control form-control--radio"},
     *     "value_options": {
     *          "adPlaced": "Yes",
     *          "adSendByPost": "No (operator to post)",
     *          "adPlacedLater": "No (operator to upload)"
     *      },
     * })
     * @Form\ErrorMessage("advertisements_adPlaced-error")
     */
    public $radio = null;

    /**
     * @Form\ComposedObject("\Common\Form\Model\Form\Lva\Fieldset\AdvertisementsAdPlacedNow")
     */
    public $adPlacedContent = null;

    /**
     * @Form\Attributes({"data-container-class":"ad-send-by-post"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $adSendByPostContent = null;

    /**
     * @Form\Attributes({"data-container-class":"ad-upload-later"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $adPlacedLaterContent = null;

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
     *          "context_field": "radio",
     *          "context_values": {"adPlaced"},
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
}
