<?php

namespace Common\Service\Qa;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FieldsetPopulatorProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FieldsetPopulatorProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fieldsetPopulatorProvider = new FieldsetPopulatorProvider();

        $populators = [
            'checkbox' => 'QaCheckboxFieldsetPopulator',
            'text' => 'QaTextFieldsetPopulator',
            'radio' => 'QaRadioFieldsetPopulator',
            'ecmt_st_no_of_permits' => 'QaEcmtShortTermNoOfPermitsFieldsetPopulator',
            'ecmt_st_permit_usage' => 'QaEcmtShortTermPermitUsageFieldsetPopulator',
            'ecmt_st_restricted_countries' => 'QaEcmtShortTermRestrictedCountriesFieldsetPopulator',
        ];

        foreach ($populators as $type => $serviceName) {
            $fieldsetPopulatorProvider->registerPopulator(
                $type,
                $serviceLocator->get($serviceName)
            );
        }

        return $fieldsetPopulatorProvider;
    }
}
