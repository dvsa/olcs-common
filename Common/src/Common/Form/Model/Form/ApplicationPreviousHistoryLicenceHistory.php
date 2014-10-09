<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_previous-history_licence-history")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationPreviousHistoryLicenceHistory
{

    /**
     * @Form\Name("dataLicencesCurrent")
     * @Form\Options({"label":"application_previous-history_licence-history.title"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\DataLicencesCurrent")
     */
    public $dataLicencesCurrent = null;

    /**
     * @Form\Name("table-licences-current")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TableLicencesCurrent")
     */
    public $tableLicencesCurrent = null;

    /**
     * @Form\Name("dataLicencesApplied")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\DataLicencesApplied")
     */
    public $dataLicencesApplied = null;

    /**
     * @Form\Name("table-licences-applied")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TableLicencesApplied")
     */
    public $tableLicencesApplied = null;

    /**
     * @Form\Name("dataLicencesRefused")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\DataLicencesRefused")
     */
    public $dataLicencesRefused = null;

    /**
     * @Form\Name("table-licences-refused")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TableLicencesRefused")
     */
    public $tableLicencesRefused = null;

    /**
     * @Form\Name("dataLicencesRevoked")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\DataLicencesRevoked")
     */
    public $dataLicencesRevoked = null;

    /**
     * @Form\Name("table-licences-revoked")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TableLicencesRevoked")
     */
    public $tableLicencesRevoked = null;

    /**
     * @Form\Name("dataLicencesPublicInquiry")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\DataLicencesPublicInquiry")
     */
    public $dataLicencesPublicInquiry = null;

    /**
     * @Form\Name("table-licences-public-inquiry")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TableLicencesPublicInquiry")
     */
    public $tableLicencesPublicInquiry = null;

    /**
     * @Form\Name("dataLicencesDisqualified")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\DataLicencesDisqualified")
     */
    public $dataLicencesDisqualified = null;

    /**
     * @Form\Name("table-licences-disqualified")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TableLicencesDisqualified")
     */
    public $tableLicencesDisqualified = null;

    /**
     * @Form\Name("dataLicencesHeld")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\DataLicencesHeld")
     */
    public $dataLicencesHeld = null;

    /**
     * @Form\Name("table-licences-held")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TableLicencesHeld")
     */
    public $tableLicencesHeld = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

