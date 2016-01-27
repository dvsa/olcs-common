<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Licence History Assets
 */
class LicenceHistoryAssets
{
    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *     "label": "application_previous-history_licence-history_prevPurchasedAssets",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "fieldset-attributes" : {
     *          "class":"checkbox inline"
     *     }
     * })
     * @Form\Type("radio")
     * @Form\Validator({
     *     "name":"Common\Form\Elements\Validators\LicenceHistoryLicenceValidator",
     *     "options": {"table": "prevPurchasedAssets-table"}
     *})
     */
    public $prevPurchasedAssets = null;

    /**
     * @Form\Name("prevPurchasedAssets-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $prevPurchasedAssetsTable = null;
}