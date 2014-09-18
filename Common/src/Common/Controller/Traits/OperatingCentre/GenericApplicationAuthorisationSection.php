<?php

/**
 * Generic Application Authorisation Section
 *
 * Internal/External - Application Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\OperatingCentre;

/**
 * Generic Application Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericApplicationAuthorisationSection
{
    use GenericAuthorisationSection;

    /**
     * Holds the sub action service
     *
     * @var string
     */
    protected $sharedActionService = 'ApplicationOperatingCentre';

    /**
     * Holds the section service
     *
     * @var string
     */
    protected $sharedService = 'Application';

    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $sharedDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'totCommunityLicences',
            'totAuthVehicles',
            'totAuthTrailers',
        ),
        'children' => array(
            'licence' => array(
                'properties' => array(
                    'id'
                ),
                'children' => array(
                    'trafficArea' => array(
                        'properties' => array(
                            'id',
                            'name'
                        )
                    )
                )
            ),
            'operatingCentre' => array(
                'properties' => array(
                    'id',
                    'version'
                ),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array('id')
                            )
                        )
                    ),
                    'adDocuments' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'filename',
                            'identifier',
                            'size'
                        )
                    )
                )
            )
        )
    );

    /**
     * Retrieve the relevant table data as we want to render it on the review summary page
     * Note that as with most controllers this is the same data we want to render on the
     * normal form page, hence why getFormTableData (declared later) simply wraps this
     */
    protected static function getSummaryTableData($id, $context, $tableName)
    {
        $data = $context->makeRestCall(
            'ApplicationOperatingCentre',
            'GET',
            array('application' => $id),
            static::$tableDataBundle
        );

        return static::formatSummaryTableData($data);
    }

    /**
     * Get operating centres count
     *
     * @return int
     */
    protected function getOperatingCentresCount()
    {
        $operatingCentres = $this->makeRestCall(
            $this->sharedActionService,
            'GET',
            array('application' => $this->getIdentifier()),
            $this->ocCountBundle
        );

        return $operatingCentres['Count'];
    }
}
