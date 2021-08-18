<?php

namespace Common\FormService\Form\Lva\OperatingCentres;

use Laminas\Form\Form;

/**
 * @see \CommonTest\FormService\Form\Lva\OperatingCentres\LicenceOperatingCentresTest
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

    /**
     * @inheritDoc
     */
    protected function alterFormForGoodsLicences(Form $form, array $params): void
    {
        parent::alterFormForGoodsLicences($form, $params);
        $this->alterFormWithTranslationKey($form, 'community-licence-changes-contact-office');

        if (is_null($params['totAuthLgvVehicles'] ?? null)) {
            $this->disableVehicleClassifications($form);
        }
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
        if ($form->get('data')->has('totCommunityLicencesFieldset')) {
            $this->getFormHelper()->disableElement($form, 'data->totCommunityLicencesFieldset->totCommunityLicences');
            $this->getFormHelper()->lockElement(
                $form->get('data')->get('totCommunityLicencesFieldset')->get('totCommunityLicences'),
                $translationKey
            );
        }
    }
}
