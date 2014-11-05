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
     * @Form\Name("current")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryCurrent")
     */
    public $current = null;

    /**
     * @Form\Name("applied")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryApplied")
     */
    public $applied = null;

    /**
     * @Form\Name("refused")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryRefused")
     */
    public $refused = null;

    /**
     * @Form\Name("revoked")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryRevoked")
     */
    public $revoked = null;

    /**
     * @Form\Name("public-inquiry")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryPublicInquiry")
     */
    public $publicInquiry = null;

    /**
     * @Form\Name("disqualified")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryDisqualified")
     */
    public $disqualified = null;

    /**
     * @Form\Name("held")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceHistoryHeld")
     */
    public $held = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     */
    public $formActions = null;
}
