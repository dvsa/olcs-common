<?php

namespace Common\FormService\Form\Lva\OperatingCentres;

use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Common\Service\Table\TableBuilder;
use Common\Service\Table\TableFactory;
use Laminas\Form\Form;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * @see \CommonTest\FormService\Form\Lva\OperatingCentres\LicenceOperatingCentresTest
 */
class LicenceOperatingCentres extends AbstractOperatingCentres
{
    protected FormHelperService $formHelper;

    protected AuthorizationService $authService;

    protected $tableBuilder;

    protected FormServiceManager $formServiceLocator;

    public function __construct(
        FormHelperService $formHelper,
        AuthorizationService $authService,
        $tableBuilder,
        FormServiceManager $formServiceLocator
    ) {
        $this->authService = $authService;
        $this->tableBuilder = $tableBuilder;
        $this->formServiceLocator = $formServiceLocator;
        parent::__construct($formHelper);
    }

    protected function alterForm(Form $form, array $params)
    {
        $this->formServiceLocator->get('lva-licence')->alterForm($form);
        return parent::alterForm($form, $params);
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
     * @param string $translationKey
     * @return void
     */
    protected function alterFormWithTranslationKey(Form $form, $translationKey)
    {
        if ($form->get('data')->has('totCommunityLicencesFieldset')) {
            $this->formHelper->disableElement($form, 'data->totCommunityLicencesFieldset->totCommunityLicences');
            $this->formHelper->lockElement(
                $form->get('data')->get('totCommunityLicencesFieldset')->get('totCommunityLicences'),
                $translationKey
            );
        }
    }
}
