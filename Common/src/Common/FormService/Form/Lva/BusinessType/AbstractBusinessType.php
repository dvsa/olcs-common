<?php

/**
 * Abstract Business Type Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\BusinessType;

use Common\FormService\Form\Lva\AbstractLvaFormService;
use Laminas\Form\Form;

/**
 * Abstract Business Type Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractBusinessType extends AbstractLvaFormService
{
    protected $lva;

    public function getForm($inForceLicences, bool $hasOrganisationSubmittedLicenceApplication)
    {
        $form = $this->getFormHelper()->createForm('Lva\BusinessType');

        $params = [
            'inForceLicences' => $inForceLicences,
            'hasOrganisationSubmittedLicenceApplication' => $hasOrganisationSubmittedLicenceApplication,
        ];

        $this->alterForm($form, $params);

        return $form;
    }

    protected function alterForm(Form $form, $params)
    {
        // Noop
    }

    protected function lockForm(Form $form, $removeStandardActions = true)
    {
        $element = $form->get('data')->get('type');

        $this->getFormHelper()->lockElement($element, 'business-type.locked');

        $this->getFormHelper()->disableElement($form, 'data->type');

        $this->getServiceLocator()->get('Helper\Guidance')->append('business-type.locked.message');

        if ($removeStandardActions) {
            $this->removeStandardFormActions($form);
            $this->addBackToOverviewLink($form, $this->lva);
        }
    }
}
