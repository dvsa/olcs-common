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
use ZfcRbac\Service\AuthorizationService;
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

    protected function setUp(): void
    {
        $this->serviceManager = $this->setUpServiceManager();
    }

    protected function setUpDefaultServices()
    {
        $this->formServiceManager();
        $this->translator();
        $this->translationHelperService();
        $this->tableBuilder();
        $this->authorizationService();
        $this->config();
        $this->filterManager();
        $this->validatorManager();
        $this->formElementManager();
        $this->formAnnotationBuilder();
        $this->formHelper();
    }

    /**
     * @return FormServiceManager
     */
    protected function formServiceManager(): FormServiceManager
    {
        if (!$this->serviceManager()->has('FormServiceManager')) {
            $instance = new FormServiceManager();
            $instance->setServiceLocator($this->serviceManager());
            $this->serviceManager()->setService('FormServiceManager', $instance);
        }
        return $this->serviceManager()->get('FormServiceManager');
    }

    /**
     * @return Translator
     */
    protected function translator(): Translator
    {
        if (!$this->serviceManager()->has('translator')) {
            $factory = new TranslatorServiceFactory();
            $instance = $factory->createService($this->serviceManager());
            $this->serviceManager()->setService('translator', $instance);
        }
        return $this->serviceManager()->get('translator');
    }

    /**
     * @return TranslationHelperService
     */
    protected function translationHelperService(): TranslationHelperService
    {
        if (!$this->serviceManager()->has('Helper\Translation')) {
            $instance = new TranslationHelperService();
            $instance->setServiceLocator($this->serviceManager());
            $this->serviceManager()->setService('Helper\Translation', $instance);
        }
        return $this->serviceManager()->get('Helper\Translation');
    }

    /**
     * @return TableFactory|m\MockInterface
     */
    protected function tableBuilder(): m\MockInterface
    {
        if (!$this->serviceManager()->has('Table')) {
            $instance = $this->setUpMockService(TableBuilder::class);
            $instance->allows('prepareTable')->andReturnSelf()->byDefault();
            $instance->allows('getRows')->andReturn([])->byDefault();
            $this->serviceManager()->setService('Table', $instance);
        }
        return $this->serviceManager()->get('Table');
    }

    /**
     * @return AuthorizationService|m\MockInterface
     */
    protected function authorizationService(): m\MockInterface
    {
        if (!$this->serviceManager()->has(AuthorizationService::class)) {
            $instance = $this->setUpMockService(AuthorizationService::class);
            $this->serviceManager()->setService(AuthorizationService::class, $instance);
        }
        return $this->serviceManager()->get(AuthorizationService::class);
    }

    /**
     * @return array
     */
    protected function config(): array
    {
        if (!$this->serviceManager()->has('Config')) {
            $instance = [
                'csrf' => [
                    'timeout' => 300,
                ],
            ];
            $this->serviceManager()->setService('Config', $instance);
        }
        return $this->serviceManager()->get('Config');
    }

    /**
     * @return FilterPluginManager
     */
    protected function filterManager(): FilterPluginManager
    {
        if (!$this->serviceManager()->has('FilterManager')) {
            $factory = new FilterManagerFactory();
            $instance = $factory->createService($this->serviceManager());
            $this->serviceManager()->setService('FilterManager', $instance);
        }
        return $this->serviceManager()->get('FilterManager');
    }

    /**
     * @return ValidatorPluginManager
     */
    protected function validatorManager(): ValidatorPluginManager
    {
        if (!$this->serviceManager()->has('ValidatorManager')) {
            $factory = new ValidatorManagerFactory();
            $instance = $factory->createService($this->serviceManager());
            $this->serviceManager()->setService('ValidatorManager', $instance);
        }
        return $this->serviceManager()->get('ValidatorManager');
    }

    /**
     * @return AbstractPluginManager
     */
    protected function formElementManager(): AbstractPluginManager
    {
        if (!$this->serviceManager()->has('FormElementManager')) {
            $factory = new FormElementManagerFactory();
            $instance = $factory->createService($this->serviceManager());
            $this->serviceManager()->setService('FormElementManager', $instance);
        }
        return $this->serviceManager()->get('FormElementManager');
    }

    protected function formAnnotationBuilder()
    {
        if (!$this->serviceManager()->has('FormAnnotationBuilder')) {
            $factory = new FormAnnotationBuilderFactory();
            $instance = $factory->createService($this->serviceManager());
            $this->serviceManager()->setService('FormAnnotationBuilder', $instance);
        }
        return $this->serviceManager()->get('FormAnnotationBuilder');
    }

    /**
     * @return m\MockInterface|FormHelperService
     */
    protected function formHelper()
    {
        if (!$this->serviceManager()->has('FormHelperService')) {
            $instance = new FormHelperService();
            $instance->setServiceLocator($this->serviceManager());
            $this->serviceManager()->setService('FormHelperService', $instance);
        }
        return $this->serviceManager()->get('FormHelperService');
    }

    protected function overrideFormHelperWithMock(): void
    {
        assert(! ($this->formHelper() instanceof m\MockInterface));
        $mock = m::mock($this->formHelper())->makePartial();
        $this->serviceManager()->setService('FormHelperService', $mock);
    }

    /**
     * @return array
     */
    protected function paramsForLicence(): array
    {
        return [
            'operatingCentres' => [],
            'canHaveSchedule41' => false,
            'canHaveCommunityLicences' => false,
            'isPsv' => false,
            'totAuthLgvVehicles' => 0,
            'isEligibleForLgv' => false,
            'vehicleType' => ['id' => RefData::APP_VEHICLE_TYPE_HGV],
        ];
    }

    /**
     * @return array
     */
    protected function paramsForLicenceThatIsEligibleForLgvs(): array
    {
        return array_merge(
            $this->paramsForLicence(),
            [
                'isEligibleForLgv' => true,
                'vehicleType' => ['id' => RefData::APP_VEHICLE_TYPE_MIXED]
            ]
        );
    }

    /**
     * @return array
     */
    protected function paramsForLicenceThatIsNotEligibleForLgvs(): array
    {
        return array_merge($this->paramsForLicence(), ['isEligibleForLgv' => false]);
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
        return array_merge($this->paramsForLicence(), ['isPsv' => false]);
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
