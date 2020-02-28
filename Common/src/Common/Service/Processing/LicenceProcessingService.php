<?php

/**
 * Licence Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Processing;

use Common\RefData;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Licence Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

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

        if ($licenceData['goodsOrPsv']['id'] === RefData::LICENCE_CATEGORY_GOODS_VEHICLE) {
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
