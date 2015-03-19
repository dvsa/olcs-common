<?php

/**
 * InterimHelperService.php
 */

namespace Common\Service\Helper;

use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\FeeEntityService;
use Common\Service\Data\FeeTypeDataService;
use Common\Service\Entity\CommunityLicEntityService;
use Common\Service\Data\CategoryDataService as Category;
use Common\Service\Printing\PrintSchedulerInterface;

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

        foreach ($variationOpCentres as $operatingCentre) {
            if ($operatingCentre['action'] === 'A') {
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

    /**
     * Grant interim
     *
     * @param int $applicationId
     */
    public function grantInterim($applicationId)
    {
        $interimData = $this->getInterimData($applicationId);

        // set interim status to in-force
        $dataToSave = [
            'id' => $interimData['id'],
            'version' => $interimData['version'],
            'interimStatus' => ApplicationEntityService::INTERIM_STATUS_INFORCE
        ];
        $this->getServiceLocator()->get('Entity\Application')->save($dataToSave);

        // get all vehicles for the given application and
        // set licence_vehicle.specified_date = current date/time
        list($activeDiscs, $newDiscs) = $this->processLicenceVehicleSaving($interimData);

        // all active discs, set goods_disc.ceased_date = current date/time wherever goods_disc.ceased_date is null
        if ($activeDiscs) {
            $this->processActiveDicsVoiding($activeDiscs);
        }

        if ($newDiscs) {
            // create a new pending discs record, Set the is_interim flag to 1
            $this->processNewDiscsAdding($newDiscs);
        }

        // activate, generate & print community licences
        $this->processCommunityLicences($interimData);

        // Print the interim document
        $this->printInterimDocument($interimData);
    }

    /**
     * Print the interim application document for an application.
     *
     * @param $application The application.
     */
    public function printInterimDocument($application = null)
    {
        if (is_array($application)) {
            $application = $application['id'];
        }

        $licenceProcessingService = $this->getServiceLocator()
            ->get('Processing\Licence');

        $licenceProcessingService->generateInterimDocument($application);
    }

    /**
     * Process licence vehicle saving
     *
     * @param array $interimData
     * @param array
     */
    protected function processLicenceVehicleSaving($interimData)
    {
        $activeDiscs = [];
        $newDiscs = [];
        $licenceVehicles = [];
        foreach ($interimData['licenceVehicles'] as $licenceVehicle) {
            $lv = [
                'id' => $licenceVehicle['id'],
                'version' => $licenceVehicle['version'],
                'specifiedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s')
            ];
            $licenceVehicles[] = $lv;

            // saving all active discs to void it later
            foreach ($licenceVehicle['goodsDiscs'] as $disc) {
                if (!$disc['ceasedDate']) {
                    $activeDiscs[] = $disc;
                }
            }

            // preparing to create new pending disc
            $newDiscs[] = [
                'licenceVehicle' => $licenceVehicle['id'],
                'isInterim' => 'Y'
            ];
        }
        if ($licenceVehicles) {
            $this->getServiceLocator()->get('Entity\LicenceVehicle')->multiUpdate($licenceVehicles);
        }

        return [$activeDiscs, $newDiscs];
    }

    /**
     * Process active discs voiding
     *
     * @param array $newDiscs
     */
    protected function processActiveDicsVoiding($activeDiscs)
    {
        $discsToVoid = [];
        foreach ($activeDiscs as $disc) {
            $dsc = [
                'id' => $disc['id'],
                'version' => $disc['version'],
                'ceasedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s')
            ];
            $discsToVoid[] = $dsc;
        }
        if ($discsToVoid) {
            $this->getServiceLocator()->get('Entity\GoodsDisc')->multiUpdate($discsToVoid);
        }
    }

    /**
     * Process new discs adding
     *
     * @param array $newDiscs
     */
    protected function processNewDiscsAdding($newDiscs)
    {
        $newDiscs['_OPTIONS_'] = [
            'multiple' => true
        ];
        $this->getServiceLocator()->get('Entity\GoodsDisc')->save($newDiscs);
    }

    /**
     * Process community licences
     *
     * @param array $interimData
     */
    protected function processCommunityLicences($interimData)
    {
        // activate community licences, set status to active and
        // set specified date to the current one where status = pending and licence id is current.
        $commLicsToActivate = [];
        $comLicsIds = [];
        foreach ($interimData['licence']['communityLics'] as $commLic) {
            if (isset($commLic['status']['id']) &&
                $commLic['status']['id'] == CommunityLicEntityService::STATUS_PENDING) {
                $cl = [
                    'id' => $commLic['id'],
                    'version' => $commLic['version'],
                    'status' => CommunityLicEntityService::STATUS_ACTIVE,
                    'specifiedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s')
                ];
                $commLicsToActivate[] = $cl;

                // saving community licences ids to document generation
                $comLicsIds[] = $commLic['id'];
            }
        }
        if ($commLicsToActivate) {
            $this->getServiceLocator()->get('Entity\CommunityLic')->multiUpdate($commLicsToActivate);
            // generate and print any pending community licences (take form communityLicService)
            $this->getServiceLocator()
                ->get('Helper\CommunityLicenceDocument')
                ->generateBatch($interimData['licence']['id'], $comLicsIds);
        }
    }

    /**
     * Refuse interim
     *
     * @param int $applicationId
     */
    public function refuseInterim($applicationId)
    {
        $interimData = $this->getInterimData($applicationId);

        // set interim status to refuse
        $dataToSave = [
            'id' => $interimData['id'],
            'version' => $interimData['version'],
            'interimStatus' => ApplicationEntityService::INTERIM_STATUS_REFUSED,
            'interimEnd' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s')
        ];
        $this->getServiceLocator()->get('Entity\Application')->save($dataToSave);

        $fileName = $interimData['isVariation'] ? 'GV Refused Interim Direction' : 'GV Refused Interim Licence';

        // generate document
        $document = $this->generateDocument($interimData);

        // upload document and save document fo JackRabbit
        $file = $this->uploadAndSaveRefuseDocument($document, $interimData, $fileName);

        // print document
        $this->printRefuseDocument($file, $fileName);
    }

    /**
     * Generate document
     *
     * @param array $interimData
     * @return string
     */
    protected function generateDocument($interimData)
    {
        $prefix = $interimData['niFlag'] === 'Y' ? 'NI/' : 'GB/';
        $type = $interimData['isVariation'] ? 'VAR' : 'NEW';
        $templateName = $prefix . $type . '_APP_INT_REFUSED';
        $queryData = [
            'user' => $this->getServiceLocator()->get('Entity\User')->getCurrentUser()['id'],
            'licence' => $interimData['licence']['id']
        ];
        return $this->getServiceLocator()
            ->get('Helper\DocumentGeneration')
            ->generateFromTemplate($templateName, $queryData);
    }

    /**
     * Upload and save refuse document
     *
     * @param string $document
     * @param array $interimData
     * @param string $fileName
     * @return string
     */
    protected function uploadAndSaveRefuseDocument($document, $interimData, $fileName)
    {
        $file = $this->getServiceLocator()
            ->get('Helper\DocumentGeneration')
            ->uploadGeneratedContent($document, 'documents', $fileName);

        $this->getServiceLocator()->get('Entity\Document')->createFromFile(
            $file,
            [
                'category' => Category::CATEGORY_LICENSING,
                'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
                'description' => $fileName,
                'filename' => $fileName,
                'fileExtension' => 'doc_rtf',
                'issuedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
                'isDigital' => false,
                'isScan' => false,
                'licence' => $interimData['licence']['id'],
                'application' => $interimData['id']
            ]
        );
        return $file;
    }

    /**
     * Print refuse document
     *
     * @param string $file
     * @param string $fileName
     */
    protected function printRefuseDocument($file, $fileName)
    {
        $this->getServiceLocator()->get('PrintScheduler')
            ->enqueueFile($file, $fileName, [PrintSchedulerInterface::OPTION_DOUBLE_SIDED]);
    }
}
