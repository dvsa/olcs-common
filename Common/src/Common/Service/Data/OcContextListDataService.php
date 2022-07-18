<?php

namespace Common\Service\Data;

/**
 * Class OcContextListDataService
 *
 * @package Olcs\Service\Data
 */
class OcContextListDataService implements ListDataInterface
{
    /**
     * @var LicenceOperatingCentre
     */
    private LicenceOperatingCentre $licenceOperatingCentreDataService;

    /**
     * @var ApplicationOperatingCentre
     */
    private ApplicationOperatingCentre $applicationOperatingCentreDataService;

    /**
     * @param LicenceOperatingCentre $licenceOperatingCentreDataService
     * @param ApplicationOperatingCentre $applicationOperatingCentreDataService
     */
    public function __construct(
        LicenceOperatingCentre $licenceOperatingCentreDataService,
        ApplicationOperatingCentre $applicationOperatingCentreDataService
    ) {
        $this->licenceOperatingCentreDataService = $licenceOperatingCentreDataService;
        $this->applicationOperatingCentreDataService = $applicationOperatingCentreDataService;
    }

    /**
     * Calls either the LicenceOperatingCentre List data service or  the ApplicationOperatingCentre list data service
     * to return a list of OCs associated with either the licence or application
     *
     * @param array|string $context   Context
     * @param bool         $useGroups Use groups
     *
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        if ($context == 'licence') {
            return $this->licenceOperatingCentreDataService->fetchListOptions($context, $useGroups);
        } elseif ($context == 'application') {
            return $this->applicationOperatingCentreDataService->fetchListOptions($context, $useGroups);
        }

        return [];
    }
}
