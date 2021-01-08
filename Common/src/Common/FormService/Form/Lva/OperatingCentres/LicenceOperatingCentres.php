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
    }

    protected function alterFormForPsvLicences(Form $form, array $params)
    {
        parent::alterFormForPsvLicences($form, $params);
        $this->alterFormWithTranslationKey($form, 'community-licence-changes-contact-office.psv');
    }

    protected function alterFormForGoodsLicences(Form $form)
    {
        parent::alterFormForGoodsLicences($form);
        $this->alterFormWithTranslationKey($form, 'community-licence-changes-contact-office');
    }

    /**
     * Apply a padlock to the totCommunityLicences field using the specified translation key
     *
     * @param Form $form
     * @param string $translationKey
     *
     * @return void
     */
    protected function alterFormWithTranslationKey(Form $form, $translationKey)
    {
        if ($form->get('data')->has('totCommunityLicences')) {
            $this->getFormHelper()->disableElement($form, 'data->totCommunityLicences');
            $this->getFormHelper()->lockElement(
                $form->get('data')->get('totCommunityLicences'),
                $translationKey
            );
        }
    }
}
