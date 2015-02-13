<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("community-licences-data-void")
 */
class CommunityLicencesDataVoid
{
    /**
     * @Form\Attributes({"value": "internal.community_licence.confirm_void_licences"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $confirm = null;
}
