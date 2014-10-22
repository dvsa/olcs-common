<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-licence-history")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class LicenceHistory
{
    /**
     * @Form\Name("dataLicencesCurrent")
     * @Form\Options({"label":"application_previous-history_licence-history.title"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryDataLicencesCurrent")
     * @Form\Validator({"name":"Common\Form\Elements\Validators\PreviousHistoryLicenceHistoryLicenceValidator"})
     */
    public $dataLicencesCurrent = null;

    /**
     * @Form\Name("table-licences-current")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $tableLicencesCurrent = null;

    /**
     * @Form\Name("dataLicencesApplied")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryDataLicencesApplied")
     */
    public $dataLicencesApplied = null;

    /**
     * @Form\Name("table-licences-applied")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $tableLicencesApplied = null;

    /**
     * @Form\Name("dataLicencesRefused")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryDataLicencesRefused")
     */
    public $dataLicencesRefused = null;

    /**
     * @Form\Name("table-licences-refused")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $tableLicencesRefused = null;

    /**
     * @Form\Name("dataLicencesRevoked")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryDataLicencesRevoked")
     */
    public $dataLicencesRevoked = null;

    /**
     * @Form\Name("table-licences-revoked")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $tableLicencesRevoked = null;

    /**
     * @Form\Name("dataLicencesPublicInquiry")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryDataLicencesPublicInquiry")
     */
    public $dataLicencesPublicInquiry = null;

    /**
     * @Form\Name("table-licences-public-inquiry")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $tableLicencesPublicInquiry = null;

    /**
     * @Form\Name("dataLicencesDisqualified")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryDataLicencesDisqualified")
     */
    public $dataLicencesDisqualified = null;

    /**
     * @Form\Name("table-licences-disqualified")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $tableLicencesDisqualified = null;

    /**
     * @Form\Name("dataLicencesHeld")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryDataLicencesHeld")
     */
    public $dataLicencesHeld = null;

    /**
     * @Form\Name("table-licences-held")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $tableLicencesHeld = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     */
    public $formActions = null;
}
