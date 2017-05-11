<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({
 *     "label": "ADDRESS OF OC"
 * })
 * @Form\Name("OperatingCentre")
 */
class UploadEvidenceOperatingCentre
{
    /**
     * @Form\Type("Hidden")
     */
    public $aocId = null;

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
     *     "legend-attributes": {"class": "form-element__label"},
     *     "label_attributes": {"class": "form-element__label"},
     *     "create_empty_option": true,
     *     "render_delimiters": false,
     *     "fieldset-attributes":{
     *          "id":"adPlacedDate_day"
     *      }
     * })
     * @Form\Filter({"name": "DateSelectNullifier"})
     * @Form\Type("DateSelect")
     * @Form\Validator({"name": "\Common\Validator\Date"})
     * @Form\Validator({"name": "Date","options":{"format":"Y-m-d"}})
     */
    public $adPlacedDate = null;

    /**
     * @Form\Name("file")
     * @Form\Attributes({"id":"file"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\MultipleFileUpload")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.advertisements.file",
     *     "label_attributes": {"class": "form-element__label"}
     * })
     */
    public $file = null;

    /**
     * @Form\Type("Hidden")
     * @Form\Required(true)
     * @Form\Validator({
     *  "name":"Zend\Validator\GreaterThan",
     *  "options": {"min": 0, "message": "Please upload your advert"}
     * })
     * @Form\Validator({"name":"Zend\Validator\NotEmpty","options":{"null"}})
     */
    public $uploadFileCount = null;
}
