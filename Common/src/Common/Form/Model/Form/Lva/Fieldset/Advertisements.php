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
     *      "fieldset-attributes": {
     *          "class": "checkbox inline"
     *      },
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
     * @Form\Attributes({"id":"adPlacedDate"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.advertisements.adPlacedDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\Filter({"name": "DateSelectNullifier"})
     * @Form\Type("DateSelect")
     */
    public $adPlacedDate = null;

    /**
     * @Form\Name("file")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\MultipleFileUpload")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.advertisements.file",
     *     "hint": "application_operating-centres_authorisation-sub-action.advertisements.file.hint"
     * })
     */
    public $file = null;
}
