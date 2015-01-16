<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Trading names fieldset
 */
class TradingNames
{
    /**
     * @Form\Attributes({"id":"", "class":"add-another"})
     * @Form\Options({
     *      "hint":"<input type='submit' name='data[tradingNames][submit_add_trading_name]' value='Add another' />",
     *      "hint_at_bottom":true,
     *      "count":1,
     *      "wrapElements":false,
     *      "allow_add":true,
     *      "allow_remove":true,
     *      "target_element": {
     *          "type":"Text",
     *          "attributes": {
     *              "class": "medium"
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
}
