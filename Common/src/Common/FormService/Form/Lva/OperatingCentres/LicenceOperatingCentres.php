<?php

/**
 * Licence Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\OperatingCentres;

use Laminas\Form\Form;

/**
 * Licence Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentres extends AbstractOperatingCentres
{
    protected function alterForm(Form $form, array $params)
    {
        $this->getFormServiceLocator()->get('lva-licence')->alterForm($form);

        parent::alterForm($form, $params);

        if ($form->get('data')->has('totCommunityLicences')) {
            $this->getFormHelper()->disableElement($form, 'data->totCommunityLicences');
            $this->getFormHelper()->lockElement(
                $form->get('data')->get('totCommunityLicences'),
                'community-licence-changes-contact-office'
            );
        }
    }
}
