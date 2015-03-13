<?php

/**
 * InterimHelperService.php
 */

namespace Common\Service\Helper;

use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\FeeEntityService;
use Common\Service\Data\FeeTypeDataService;

/**
 * Class InterimHelperService
 *
 * Helper service to determine whether a variation qualifies for an interim application.
 *
 * @package Common\Service\Helper
 *
 * @author Josh Curtis <josh.curtis@valtech.com>
 */
class InterimHelperService extends AbstractHelperService
{
    /**
     * Maps data keys from the licence and variation arrays into the relevant method.
     *
     * @var array
     */
    protected $functionToDataMap = array(
        'hasUpgrade'=> 'licenceType',
        'hasAuthIncrease' => 'totAuthVehicles',
        'hasAuthIncrease' => 'totAuthTrailers',
        'hasNewOperatingCentre' => 'operatingCentres',
        'hasIncreaseInOperatingCentre' => 'operatingCentres'
    );

    /**
     * Can this variation create an interim licence application?
     *
     * @param null $applicationId
     *
     * @return bool
     */
    public function canVariationInterim($applicationId = null)
    {
        if (is_null($applicationId) || !is_int($applicationId)) {
            throw new \InvalidArgumentException(__METHOD__ . ' no application id integer given.');
        }

        $applicationData = $this->getServiceLocator()
            ->get("Entity/Application")
            ->getVariationInterimData($applicationId);

        $licenceData = $applicationData['licence'];

        foreach ($this->functionToDataMap as $function => $dataKey) {
            if ($this->$function($applicationData[$dataKey], $licenceData[$dataKey])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create an interim fee for the application if one doesnt already exist.
     *
     * @param $applicationId The applications identifier.
     *
     * @return void
     */
    public function createInterimFeeIfNotExist($applicationId)
    {
        $applicationProcessingService = $this->getServiceLocator()->get('Processing\Application');
        $fees = $this->getInterimFees($applicationId);

        // Create fee if not exist.
        if (!$fees) {
            $interimData = $this->getInterimData($applicationId);
            $applicationProcessingService->createFee(
                $applicationId,
                $interimData['licence']['id'],
                FeeTypeDataService::FEE_TYPE_GRANTINT
            );
        }
    }

    /**
     * Cancel an interim fee for an application if one exists.
     *
     * @param $applicationId The applications identifier.
     *
     * @return void
     */
    public function cancelInterimFees($applicationId)
    {
        $feeService = $this->getServiceLocator()->get('Entity\Fee');
        $fees = $this->getInterimFees($applicationId);

        $ids = [];
        foreach ($fees as $fee) {
            $ids[] = $fee['id'];
        }

        if ($ids) {
            $feeService->cancelByIds($ids);
        }

    }

    /**
     * Get all fees with a specific type and status for an application.
     *
     * @param null|int $applicationId The application identifier.
     * @param string $type The fee type.
     * @param array $statuses The fee statuses.
     *
     * @return mixed
     */
    protected function getInterimFees
    (
        $applicationId = null,
        $type = FeeTypeDataService::FEE_TYPE_GRANTINT,
        $statuses = array(
            FeeEntityService::STATUS_OUTSTANDING,
            FeeEntityService::STATUS_WAIVE_RECOMMENDED
        )
    ) {
        $feeService = $this->getServiceLocator()->get('Entity\Fee');

        $applicationProcessingService = $this->getServiceLocator()->get('Processing\Application');
        $feeTypeData = $applicationProcessingService->getFeeTypeForApplication(
            $applicationId,
            $type
        );

        return $feeService->getFeeByTypeStatusesAndApplicationId(
            $feeTypeData['id'],
            $statuses,
            $applicationId
        );
    }

    /**
     * Get interim data for an application.
     *
     * @param $applicationId Get interim data
     *
     * @return mixed
     */
    protected function getInterimData($applicationId)
    {
        return $this->getServiceLocator()
            ->get('Entity\Application')
            ->getDataForInterim($applicationId);
    }

    /**
     * Determine whether the licence has changed within set parameters that would
     * qualify this variation to be an interim.
     *
     * @param $variation The variation data.
     * @param $licence The current licence data.
     *
     * @return bool
     */
    protected function hasUpgrade($variation, $licence)
    {
        // If licence type has been changed from restricted to national or international.
        if (
            $licence['id'] === LicenceEntityService::LICENCE_TYPE_RESTRICTED &&
            (
                $variation['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL ||
                $variation['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ) {
            return true;
        }

        // If licence is is updated from a standard national to an international.
        if (
            $licence['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL &&
            (
                $variation['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Has the overall authority increased.
     *
     * @param $variation The variation data.
     * @param $licence The current licence data.
     *
     * @return bool
     */
    protected function hasAuthIncrease($variation, $licence)
    {
        return ($variation > $licence);
    }

    /**
     * Does this variation specify an additional operating centre.
     *
     * @param $variationOpCentres The variation data.
     * @param $licenceOpCentres The current licence data.
     *
     * @return bool
     */
    protected function hasNewOperatingCentre($variationOpCentres, $licenceOpCentres)
    {
        if (empty($variationOpCentres)) {
            return false;
        }

        foreach($variationOpCentres as $operatingCentre) {
            if($operatingCentre['action'] === 'A') {
                return true;
            }
        }

        return false;
    }

    /**
     * Does this variation increment an operating centres vehicles or trailers.
     *
     * @param $variation The variation data.
     * @param $licence The current licence data.
     *
     * @return bool
     */
    protected function hasIncreaseInOperatingCentre($variationOpCentres, $licenceOpCentres)
    {
        $licence = array();
        $variation = array();

        // Makes dealing with the records easier.
        foreach ($licenceOpCentres as $opCentre) {
            $licence[$opCentre['operatingCentre']['id']] = $opCentre;
        }

        foreach ($variationOpCentres as $opCentre) {
            $variation[$opCentre['operatingCentre']['id']] = $opCentre;
        }

        // foreach of the licence op centres.
        foreach ($licence as $key => $operatingCenter) {
            // If a variation record doesnt exists or its a removal op centre.
            if (!isset($variation[$key]) || $variation[$key]['action'] == 'D') {
                break;
            }

            if (
                ($variation[$key]['noOfVehiclesRequired'] > $licence[$key]['noOfVehiclesRequired']) ||
                ($variation[$key]['noOfTrailersRequired'] > $licence[$key]['noOfTrailersRequired'])
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the function to data map.
     *
     * @param array $functionToDataMap The function to data map.
     *
     * @return $this
     */
    public function setFunctionToDataMap(array $functionToDataMap)
    {
        $this->functionToDataMap = $functionToDataMap;

        return $this;
    }
}
