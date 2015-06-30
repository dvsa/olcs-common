<?php

/**
 * Licence Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Processing;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Data\CategoryDataService;

/**
 * Licence Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function generateInterimDocument($applicationId)
    {
        $application = $this->getServiceLocator()
            ->get('Entity\Application')
            ->getDataForProcessing($applicationId);

        $licenceId = $application['licence']['id'];

        if ($application['isVariation']) {
            $template = 'GV_INT_DIRECTION_V1';
            $description = "GV Interim Direction";
        } else {
            $template = 'GV_INT_LICENCE_V1';
            $description = "GV Interim Licence";
        }

        $storedFile = $this->getServiceLocator()
            ->get('Helper\DocumentGeneration')
            ->generateAndStore(
                $template,
                $description,
                [
                    'application' => $applicationId,
                    'licence' => $licenceId
                ]
            );

        $this->getServiceLocator()->get('Helper\DocumentDispatch')->process(
            $storedFile,
            [
                'description' => $description,
                'filename'    => str_replace(" ", "_", $description) . '.rtf',
                'application' => $applicationId,
                'licence'     => $licenceId,
                'category'    => CategoryDataService::CATEGORY_LICENSING,
                'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
                'isExternal'  => false,
                'isScan'      => false
            ]
        );
    }

    /**
     * Void all active discs attached to a licence
     *
     * @param int $licenceId Licence ID
     */
    public function voidAllDiscs($licenceId)
    {
        $licenceData = $this->getServiceLocator()->get('Entity\Licence')->getRevocationDataForLicence($licenceId);

        $this->getServiceLocator()->get('Helper\LicenceStatus')->ceaseDiscs($licenceData);
    }

    /**
     * Create new discs for a licence
     *
     * @param int $licenceId     Licence ID
     * @param int $numberOfDiscs Number of discs to create (Only applicable for PSV)
     */
    public function createDiscs($licenceId, $numberOfDiscs = null)
    {
        $licenceData = $this->getServiceLocator()->get('Entity\Licence')->getRevocationDataForLicence($licenceId);

        if ($licenceData['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            $this->getServiceLocator()->get('Entity\GoodsDisc')->createForVehicles($licenceData['licenceVehicles']);
        } else {
            $this->getServiceLocator()->get('Entity\PsvDisc')->requestBlankDiscs($licenceId, $numberOfDiscs);
        }
    }

    /**
     * Void all community licences attached to a licence
     *
     * @param int $licenceId     Licence ID
     */
    public function voidAllCommunityLicences($licenceId)
    {
        $this->getServiceLocator()->get('Processing\Application')->voidCommunityLicencesForLicence($licenceId);
    }

    /**
     * Create a number of Community Licences for a Licence
     *
     * @param int $licenceId      Licence ID
     * @param int $numberToCreate Number of Community Licences to create
     */
    public function createCommunityLicences($licenceId, $numberToCreate)
    {
        $this->getServiceLocator()->get('LicenceCommunityLicenceAdapter')
            ->addCommunityLicences($licenceId, $numberToCreate, null);

        $this->getServiceLocator()->get('Entity\Licence')->updateCommunityLicencesCount($licenceId);
    }

    /**
     * Create a Community Licences Office Copy for a Licence
     *
     * @param int $licenceId      Licence ID
     */
    public function createCommunityLicenceOfficeCopy($licenceId)
    {
        $this->getServiceLocator()->get('LicenceCommunityLicenceAdapter')
            ->addOfficeCopy($licenceId, null);
    }
}
