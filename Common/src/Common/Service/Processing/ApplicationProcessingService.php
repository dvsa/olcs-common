<?php

/**
 * Application Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Processing;

use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\LicenceEntityService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Application Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function validateApplication($id)
    {
        $licenceId = $this->getLicenceId($id);

        $this->setApplicationStatus($id, ApplicationEntityService::APPLICATION_STATUS_VALID);

        $this->copyApplicationDataToLicence($id, $licenceId);

        $this->processApplicationOperatingCentres($id, $licenceId);

        $this->createDiscRecords($licenceId);

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addSuccessMessage('licence-valid-confirmation');
    }

    protected function processApplicationOperatingCentres($id, $licenceId)
    {
        $applicationOperatingCentres = $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')
            ->getForApplication($id);

        $new = $updates = $deletions = array();

        foreach ($applicationOperatingCentres as $aoc) {
            switch ($aoc['action']) {
                case 'A':
                    $aoc['operatingCentre'] = $aoc['operatingCentre']['id'];
                    $aoc['licence'] = $licenceId;
                    $new[] = $aoc;
                    break;
            }
        }

        if (!empty($new)) {
            $licenceOperatingCentreService = $this->getServiceLocator()->get('Entity\LicenceOperatingCentre');
            foreach ($new as $aoc) {
                $licenceOperatingCentreService->save($aoc);
            }
        }

        // @todo Process updates and deletions (Out of scope for OLCS-4895)
    }

    protected function createDiscRecords($licenceId)
    {
        $licenceVehicles = $this->getServiceLocator()->get('Entity\LicenceVehicle')
            ->getForApplicationValidation($licenceId);

        if (!empty($licenceVehicles)) {
            $category = $this->getServiceLocator()->get('Entity\Licence')->getCategory($licenceId);

            if ($category === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
                $this->createGoodsDiscs($licenceVehicles);
            } else {
                $this->createPsvDiscs($licenceId, count($licenceVehicles));
            }
        }
    }

    protected function createGoodsDiscs($licenceVehicles)
    {
        $defaults = array(
            'ceasedDate' => null,
            'issuedDate' => null,
            'discNo' => null,
            'isCopy' => 'N'
        );

        $goodsDiscService = $this->getServiceLocator()->get('Entity\GoodsDisc');

        foreach ($licenceVehicles as $licenceVehicle) {
            $data = array_merge(
                $defaults,
                array(
                    'licenceVehicle' => $licenceVehicle['id']
                )
            );
            $goodsDiscService->save($data);
        }
    }

    protected function createPsvDiscs($licenceId, $count)
    {
        $data = array(
            'licence' => $licenceId,
            'ceasedDate' => null,
            'issuedDate' => null,
            'discNo' => null,
            'isCopy' => 'N'
        );

        $this->getServiceLocator()->get('Entity\PsvDisc')->requestDiscs($count, $data);
    }

    protected function copyApplicationDataToLicence($id, $licenceId)
    {
        $licenceData = array_merge(
            array(
                'status' => LicenceEntityService::LICENCE_STATUS_VALID
            ),
            $this->getImportantLicenceDate(),
            $this->getApplicationDataForValidating($id)
        );
        $this->getServiceLocator()->get('Entity\Licence')->forceUpdate($licenceId, $licenceData);
    }

    protected function getApplicationDataForValidating($id)
    {
        return $this->getServiceLocator()->get('Entity\Application')->getDataForValidating($id);
    }

    protected function getImportantLicenceDate()
    {
        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();
        $reviewDate = date('Y-m-d', strtotime($date . ' +5 years'));
        $dom = date('j', strtotime($date));
        $expiryDate = date('Y-m-d', strtotime($date . ' +5 years -' . $dom . ' days'));

        return array(
            'inForceDate' => $date,
            'reviewDate' => $reviewDate,
            'expiryDate' => $expiryDate,
            'feeDate' => $expiryDate
        );
    }

    protected function setApplicationStatus($id, $status)
    {
        $data = array('status' => $status);

        $this->getServiceLocator()->get('Entity\Application')->forceUpdate($id, $data);
    }

    protected function getLicenceId($id)
    {
        return $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($id);
    }
}
