<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("share-info")
 */
class ShareInfo
{
    /**
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "label": "licence.vehicles-trailers.share-info",
     *     "label_attributes": {"id": "label-shareInfo"}
     * })
     * @Form\Attributes({"data-container-class": "confirm"})
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $shareInfo = null;
}
