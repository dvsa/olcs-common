<?php

/**
 * ContinueLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Common\Service\Entity\LicenceEntityService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * ContinueLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ContinueLicence implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Continue a Licence
     *
     * @param int $params['continuationDetailId'] ContinuationDetail ID
     *
     * @return Response
     */
    public function process(array $params)
    {
        if (!isset($params['continuationDetailId'])) {
            return new Response(Response::TYPE_FAILED, [], "'continuationDetailId' parameter is missing.");
        }
        $continuationDetailId = (int) $params['continuationDetailId'];

        $continuationDetailService = $this->getServiceLocator()->get('Entity\ContinuationDetail');
        $continuationDetail = $continuationDetailService->getDetailsForProcessing($continuationDetailId);

        $licence = $continuationDetail['licence'];
        $licenceId = $licence['id'];

        //Add 5 years to the continuation and review dates
        $updatedLicence = [
            'id' => $licenceId,
            'version' => $licence['version'],
            'expiryDate' => (new \DateTime($licence['expiryDate']))->modify('+5 years')->format('Y-m-d'),
            'reviewDate' => (new \DateTime($licence['reviewDate']))->modify('+5 years')->format('Y-m-d'),
        ];
        $this->getServiceLocator()->get('Entity\Licence')->save($updatedLicence);

        /* @var $licenceProcessingService \Common\Service\Processing\LicenceProcessingService */
        $licenceProcessingService = $this->getServiceLocator()->get('Processing\Licence');

        // If PSV (excluding PSV Special restricted):
        if ($licence['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_PSV
            && $licence['licenceType']['id'] !== LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
        ) {
            // Update the vehicle authorisation to the value entered
            $this->getServiceLocator()->get('Entity\Licence')->forceUpdate(
                $licenceId,
                ['totAuthVehicles' => $continuationDetail['totAuthVehicles']]
            );

            // Void any discs
            $licenceProcessingService->voidAllDiscs($licenceId);

            // Create 'X' new PSV discs where X is the number of discs requested
            $licenceProcessingService->createDiscs($licenceId, $continuationDetail['totPsvDiscs']);

            // If licence type is Restricted or Standard International
            if ($licence['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_RESTRICTED
                || $licence['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            ) {
                //Void all community licences
                $licenceProcessingService->voidAllCommunityLicences($licenceId);

                // Generate 'X' new Community licences where X is the number of community licences requested
                $licenceProcessingService->createCommunityLicences(
                    $licenceId,
                    $continuationDetail['totCommunityLicences']
                );
                // plus the office copy
                $licenceProcessingService->createCommunityLicenceOfficeCopy($licenceId);
            }
        }

        //If Goods
        if ($licence['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            //Void any discs
            $licenceProcessingService->voidAllDiscs($licenceId);

            //Create a new Goods disc for each vehicle that has a specified date (and is not ceased)
            $licenceProcessingService->createDiscs($licenceId);

            //If licence type is Standard International
            if ($licence['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL) {
                //Void all community licences
                $licenceProcessingService->voidAllCommunityLicences($licenceId);

                //Generate 'X' new Community licences where X is the number of community licences requested
                $licenceProcessingService->createCommunityLicences(
                    $licenceId,
                    $continuationDetail['totCommunityLicences']
                );

                // plus the office copy
                $licenceProcessingService->createCommunityLicenceOfficeCopy($licenceId);
            }
        }

        // @todo this has been migrated - Don't forget to re-use
        $licenceProcessingService->generateDocument($licenceId);

        // Set the status of the continuation record to 'Complete'
        $this->getServiceLocator()->get('Entity\ContinuationDetail')->forceUpdate(
            $continuationDetail['id'],
            ['status' => \Common\Service\Entity\ContinuationDetailEntityService::STATUS_COMPLETE]
        );

        return new Response(Response::TYPE_SUCCESS);
    }
}
