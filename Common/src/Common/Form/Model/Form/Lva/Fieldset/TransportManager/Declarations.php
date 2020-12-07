<?php

namespace Common\Form\Model\Form\Lva\Fieldset\TransportManager;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("lva-transport-manager-declarations")
 */
class Declarations
{
    /**
     * @Form\Attributes({"value":"<strong>%s</strong>"})
     * @Form\Options({"tokens":{"lva-tm-details-details-declarations-by-submitting"}})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $bySubmitted = null;

    /**
     * @Form\Attributes({"value":"<strong>%s</strong>", "data-container-class": "js-hidden"})
     * @Form\Options({"tokens":{"lva-tm-details-details-declarations-internal-header"}})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $internalHeader = null;

    /**
     * @Form\Attributes({"data-container-class": "tm-details-declaration-internal"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $internal = null;

    /**
     * @Form\Attributes({"value":"<strong>%s</strong>", "data-container-class": "js-hidden"})
     * @Form\Options({"tokens":{"lva-tm-details-details-declarations-external-header"}})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $externalHeader = null;

    /**
     * @Form\Attributes({"data-container-class": "tm-details-declaration-external"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $external = null;

    /**
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "must_be_value": "Y"
     * })
     * @Form\Attributes({"data-container-class": "confirm checkbox"})
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $confirmation = null;
}
