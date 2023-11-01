<?php

declare(strict_types=1);

namespace Common\Test\FormService\Form\Lva\OperatingCentres;

use Olcs\TestHelpers\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Laminas\Mvc\I18n\Translator;
use Laminas\Mvc\Service\TranslatorServiceFactory;
use Common\RefData;
use Common\Service\Table\TableFactory;
use Common\Service\Table\TableBuilder;
use LmcRbacMvc\Service\AuthorizationService;
use Laminas\Filter\FilterPluginManager;
use Laminas\Mvc\Service\FilterManagerFactory;
use Laminas\Validator\ValidatorPluginManager;
use Laminas\Mvc\Service\ValidatorManagerFactory;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\Form\FormElementManagerFactory;
use Common\Service\FormAnnotationBuilderFactory;
use Common\Service\Helper\FormHelperService;
use Mockery as m;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\TranslationHelperService;
use Laminas\Form\Form;
use Laminas\View\Renderer\PhpRenderer;

abstract class OperatingCentresTestCase extends MockeryTestCase
{
    use MocksServicesTrait;

    protected const LGV_FIELDSET_NAME = 'totAuthLgvVehiclesFieldset';
    protected const HGV_FIELDSET_LABEL_WITH_VEHICLE_CLASSIFICATIONS_DISABLED = 'application_operating-centres_authorisation.data.totAuthHgvVehiclesFieldset.vehicles-label';
    protected const HGV_FIELDSET_LABEL_WITH_VEHICLE_CLASSIFICATIONS_ENABLED = 'application_operating-centres_authorisation.data.totAuthHgvVehiclesFieldset.hgvs-label';
    protected const HGV_FIELD_LABEL_WITH_VEHICLE_CLASSIFICATIONS_DISABLED = 'application_operating-centres_authorisation.data.totAuthHgvVehicles.vehicles-label';
    protected const HGV_FIELD_LABEL_WITH_VEHICLE_CLASSIFICATIONS_ENABLED = 'application_operating-centres_authorisation.data.totAuthHgvVehicles.hgvs-label';
    protected const HGV_FIELD_NAME = 'totAuthHgvVehicles';
    protected const HGV_FIELDSET_NAME = 'totAuthHgvVehiclesFieldset';

    /** @var  \Common\Service\Helper\AddressHelperService | m\MockInterface */
    protected $mockHlpAddr;
    /** @var  \Common\Service\Helper\DateHelperService | m\MockInterface */
    protected $mockHlpDate;
    /** @var  \Common\Service\Data\AddressDataService| m\MockInterface */
    protected $mockDataAddress;

    protected $formServiceLocator;
    protected $translator;
    protected $urlHelper;
    protected $tableBuilder;
    protected $formHelper;
    protected $authService;


    protected function setUpDefaultServices()
    {
        $this->formServiceLocator = m::mock(FormServiceManager::class);
        $this->translator = m::mock(TranslationHelperService::class);
        $this->tableBuilder = m::mock(TableFactory::class);
        $this->authService = m::mock(AuthorizationService::class);
        $this->filterManager = m::mock(FilterPluginManager::class);
        $this->formHelper = m::mock(FormHelperService::class);
    }



    /**
     * @return array
     */
    protected function paramsForLicence(): array
    {
        return $this->paramsForHgvLicence();
    }

    /**
     * @return array
     */
    protected function paramsForHgvLicence(): array
    {
        return [
            'operatingCentres' => [],
            'canHaveSchedule41' => false,
            'canHaveCommunityLicences' => false,
            'isPsv' => false,
            'vehicleType' => ['id' => RefData::APP_VEHICLE_TYPE_HGV],
        ];
    }

    /**
     * @return array
     */
    protected function paramsForMixedLicenceWithoutLgv(): array
    {
        return array_merge(
            $this->paramsForLicence(),
            [
                'vehicleType' => ['id' => RefData::APP_VEHICLE_TYPE_MIXED],
                'totAuthLgvVehicles' => null,
            ]
        );
    }

    /**
     * @return array
     */
    protected function paramsForMixedLicenceWithLgv(): array
    {
        return array_merge(
            $this->paramsForMixedLicenceWithoutLgv(),
            [
                'totAuthLgvVehicles' => 0,
            ]
        );
    }

    /**
     * @return array
     */
    protected function paramsForLicenceThatAreEligibleForCommunityLicences()
    {
        return array_merge($this->paramsForLicence(), ['canHaveCommunityLicences' => true]);
    }

    /**
     * @return array
     */
    protected function paramsForLicenceThatAreNotEligibleForCommunityLicences()
    {
        return array_merge($this->paramsForLicence(), ['canHaveCommunityLicences' => false]);
    }

    /**
     * @return array
     */
    protected function paramsForGoodsLicence(): array
    {
        return $this->paramsForHgvLicence();
    }

    /**
     * @return array
     */
    protected function paramsForPsvLicence(): array
    {
        return array_merge($this->paramsForLicence(), ['isPsv' => true]);
    }

    /**
     * @return array
     */
    protected function paramsForPsvLicenceThatAreEligibleForCommunityLicences(): array
    {
        return array_merge($this->paramsForPsvLicence(), ['canHaveCommunityLicences' => true]);
    }

    /**
     * @param Form $form
     */
    protected function assertVehicleClassificationsAreDisabledForForm(Form $form): void
    {
        $dataFieldset = $form->get('data');

        $this->assertFalse($dataFieldset->has(static::LGV_FIELDSET_NAME), 'Expected LGV fieldset to have been removed from the form');

        $hgvFieldset = $dataFieldset->get(static::HGV_FIELDSET_NAME);
        $this->assertSame(static::HGV_FIELDSET_LABEL_WITH_VEHICLE_CLASSIFICATIONS_DISABLED, $hgvFieldset->getLabel());

        $hgvField = $hgvFieldset->get(static::HGV_FIELD_NAME);
        $this->assertSame(static::HGV_FIELD_LABEL_WITH_VEHICLE_CLASSIFICATIONS_DISABLED, $hgvField->getLabel());
    }

    /**
     * @param Form $form
     */
    protected function assertVehicleClassificationsAreEnabledForForm(Form $form): void
    {
        $dataFieldset = $form->get('data');

        $this->assertTrue($dataFieldset->has(static::LGV_FIELDSET_NAME), 'Expected LGV fieldset to exist in the form');

        $hgvFieldset = $dataFieldset->get(static::HGV_FIELDSET_NAME);
        $this->assertSame(static::HGV_FIELDSET_LABEL_WITH_VEHICLE_CLASSIFICATIONS_ENABLED, $hgvFieldset->getLabel());

        $hgvField = $hgvFieldset->get(static::HGV_FIELD_NAME);
        $this->assertSame(static::HGV_FIELD_LABEL_WITH_VEHICLE_CLASSIFICATIONS_ENABLED, $hgvField->getLabel());
    }
}
