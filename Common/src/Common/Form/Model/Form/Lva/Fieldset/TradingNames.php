<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Trading names fieldset
 */
class TradingNames
{
    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *      "count":1,
     *      "wrapElements":false,
     *      "allow_add":true,
     *      "allow_remove":true,
     *      "target_element": {
     *          "type":"Text",
     *          "attributes": {
     *              "class": "long"
     *          },
     *          "options": {
     *              "wrapElements":false
     *          }
     *      }
     * })
     * @Form\Type("Collection")
     * @Form\Name("trading_name")
     */
    public $tradingName = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--tertiary","data-container-class":"inline"})
     * @Form\Options({
     *     "label": "Add another"
     * })
     * @Form\Name("submit_add_trading_name")
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $submitAddTradingName = null;
}
