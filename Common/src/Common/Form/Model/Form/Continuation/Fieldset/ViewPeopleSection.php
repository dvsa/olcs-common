<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("viewPeopleSection")
 */
class ViewPeopleSection
{
    /**
     * @Form\Attributes({"value": "markup-continuation-licence-checklist-people-header"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $peopleHeader = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--secondary large", "id": "viewPeople", "target":"_blank"})
     * @Form\Options({"label": "continuations.people.button-label."})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionLink")
     */
    public $viewPeople = null;

    /**
     * @Form\Attributes({"value": "markup-continuation-licence-checklist-people-help-message"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $message = null;
}
