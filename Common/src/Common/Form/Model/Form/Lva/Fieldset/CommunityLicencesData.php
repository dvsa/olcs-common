<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"label":""})
 */
class CommunityLicencesData
{
    /**
     * @Form\Attributes({"class":"short","id":"","disabled":"disabled"})
     * @Form\Options({"label":"application_community_licence_total_community_licences"})
     * @Form\Type("Text")
     */
    public $totalCommunityLicences = null;
}
