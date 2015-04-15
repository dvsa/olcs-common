<?php

namespace Common\Form\Model\Form\Lva\Fieldset\TransportManager;

use Zend\Form\Annotation as Form;

/**
 * NOTE: This fieldset is used for LVA and for the internal TM section
 *
 * @Form\Attributes({"class":""})
 * @Form\Name("tm-convictions-and-penalties-details")
 */
class ConvictionsAndPenaltiesDetails
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
     * @Form\Attributes({"id":"conviction-date","required":false,"class":"long"})
     * @Form\Options({
     *     "label": "transport-manager.convictions-and-penalties.form.conviction-date",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\Type("DateSelect")
     * @Form\Filter({"name":"DateSelectNullifier"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     * @Form\Validator({"name": "\Zend\Validator\NotEmpty"})
     * @Form\Validator({"name":"\Common\Form\Elements\Validators\DateNotInFuture"})
     */
    public $convictionDate = null;

    /**
     * @Form\Attributes({"class":"long","id":"category-text"})
     * @Form\Options({"label":"transport-manager.convictions-and-penalties.form.offence"})
     * @Form\Type("Text")
     * @Form\Validator({
     *      "name":"Zend\Validator\NotEmpty"
     * })
     */
    public $categoryText = null;

    /**
     * @Form\Attributes({"class":"long","id":"notes"})
     * @Form\Options({"label":"transport-manager.convictions-and-penalties.form.offence-details"})
     * @Form\Type("Text")
     * @Form\Validator({
     *      "name":"Zend\Validator\NotEmpty"
     * })
     */
    public $notes = null;

    /**
     * @Form\Attributes({"class":"long","id":"court-fpn"})
     * @Form\Options({"label":"transport-manager.convictions-and-penalties.form.court-fpn"})
     * @Form\Type("Text")
     * @Form\Validator({
     *      "name":"Zend\Validator\NotEmpty"
     * })
     */
    public $courtFpn = null;

    /**
     * @Form\Attributes({"class":"long","id":"penalty"})
     * @Form\Options({"label":"transport-manager.convictions-and-penalties.form.penalty"})
     * @Form\Type("Text")
     * @Form\Validator({
     *      "name":"Zend\Validator\NotEmpty"
     * })
     */
    public $penalty = null;
}
