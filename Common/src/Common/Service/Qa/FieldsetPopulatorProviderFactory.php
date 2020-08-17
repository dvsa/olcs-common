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
            'ecmt_st_no_of_permits' => 'QaEcmtNoOfPermitsFieldsetPopulator',
            'ecmt_st_permit_usage' => 'QaEcmtPermitUsageFieldsetPopulator',
            'ecmt_st_restricted_countries' => 'QaEcmtRestrictedCountriesFieldsetPopulator',
            'ecmt_st_annual_trips_abroad' => 'QaEcmtAnnualTripsAbroadFieldsetPopulator',
            'ecmt_st_international_journeys' => 'QaEcmtInternationalJourneysFieldsetPopulator',
            'ecmt_st_earliest_permit_date' => 'QaEcmtShortTermEarliestPermitDateFieldsetPopulator',
            'ecmt_rem_permit_start_date' => 'QaEcmtRemovalPermitStartDateFieldsetPopulator',
            'cert_road_mot_expiry_date' => 'QaCertRoadworthinessMotExpiryDateFieldsetPopulator',
            'bilateral_permit_usage' => 'QaBilateralPermitUsageFieldsetPopulator',
            'bilateral_cabotage_only' => 'QaBilateralCabotageOnlyFieldsetPopulator',
            'bilateral_standard_and_cabotage' => 'QaBilateralStandardAndCabotageFieldsetPopulator',
            'bilateral_number_of_permits' => 'QaBilateralNoOfPermitsFieldsetPopulator',
            'bilateral_third_country' => 'QaBilateralThirdCountryFieldsetPopulator',
            'bilateral_emissions_standards' => 'QaBilateralEmissionsStandardsFieldsetPopulator',
            'ecmt_sectors' => 'QaEcmtSectorsFieldsetPopulator',
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
