<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("form-actions")
 * @Form\Attributes({"class":"actions-container"})
 */
class TradingNames
{

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *      "count":1,
     *      "wrapElements":false,
     *      "allow_add":true,
     *      "allow_remove":true,
     *      "target_element": {
     *          "type":"\Zend\Form\Element\Text",
     *          "atributes": {
     *              "data-container-class":"block"
     *          },
     *          "options": {
     *              "wrapElements":false
     *          }
     *      }
     * })
     * @Form\Type("Collection")
     */
    public $tradingName = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--secondary large","data-container-class":"inline"})
     * @Form\Options({
     *     "label": "Add another"
     * })
     * @Form\Name("submit_add_trading_name")
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $submitAddTradingName = null;
}