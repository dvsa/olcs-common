<?php

/**
 * Transfer Goods Vehicles
 *
 * @author Alex Peshkov <alex.peshkov@valteh.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;
use Common\Service\Entity\LicenceEntityService;

/**
 * Transfer Goods Vehicles
 *
 * @author Alex Peshkov <alex.peshkov@valteh.co.uk>
 */
class TransferVehicles implements
    BusinessServiceInterface,
    ServiceLocatorAwareInterface,
    BusinessRuleAwareInterface
{
    use ServiceLocatorAwareTrait,
        BusinessRuleAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        $sourceLicenceId = $params['sourceLicenceId'];
        $targetLicenceId = $params['targetLicenceId'];
        $ids = explode(',', $params['id']);

        $targetLicence = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getLicenceWithVehicles($targetLicenceId);
        $sourceLicence = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getLicenceWithVehicles($sourceLicenceId);

        $translator = $this->getServiceLocator()->get('Helper\Translation');
        // check for total authority
        if ((count($targetLicence['licenceVehicles']) + count($ids)) > $targetLicence['totAuthVehicles']) {
            $response->setType(Response::TYPE_FAILED);
            $response->setMessage(
                sprintf(
                    $translator->translate('licence.vehicles_transfer.form.message_exceed'),
                    $targetLicence['licNo']
                )
            );
            return $response;
        }

        $sourceVehiclesIds = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getVehiclesIdsByLicenceVehiclesIds($sourceLicenceId, $ids);

        // check if vehicles already exists for the current licence
        $existedVehicles = $this->getExistedVehicles($targetLicence, $sourceVehiclesIds);
        if (count($existedVehicles)) {
            $response->setType(Response::TYPE_FAILED);
            $response->setMessage(
                sprintf(
                    $translator->translate(
                        'licence.vehicles_transfer.form.message_already_on_licence' .
                        (count($existedVehicles) == 1 ? '_singular' : '')
                    ),
                    implode(', ', $existedVehicles),
                    $targetLicence['licNo']
                )
            );
            return $response;
        }

        // transfer vehicles

        // remove existing vehicles on source licence
        $this->getServiceLocator()
            ->get('Entity\LicenceVehicle')
            ->removeVehicles($ids);

        // create vehicles on target licence
        $targetLicenceVehicles = $this->createVehicles($sourceVehiclesIds, $targetLicenceId);

        if ($targetLicence['goodsOrPsv']['id'] == LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            // remove goods discs
            $this->removeGoodsDiscs($sourceLicence, $ids);

            // create goods discs
            $this->createGoodsDiscs($targetLicenceVehicles);
        }

        return $response;
    }

    /**
     * Get existed vehicles for the licence
     *
     * @param array $targetLicence
     * @param array $sourceVehiclesIds
     * @return array
     */
    protected function getExistedVehicles($targetLicence, $sourceVehiclesIds)
    {
        $existedVehicles = [];
        foreach ($targetLicence['licenceVehicles'] as $lv) {
            if (array_search($lv['vehicle']['id'], $sourceVehiclesIds) !== false) {
                $existedVehicles[$lv['vehicle']['id']] = $lv['vehicle']['vrm'];
            }
        }
        return $existedVehicles;
    }

    /**
     * Create licence vehicle records
     *
     * @param array $sourceVehiclesIds
     * @param int $targetLicenceId
     * @return array
     */
    protected function createVehicles($sourceVehiclesIds, $targetLicenceId)
    {
        $data = [];
        $currentUserId = $this->getServiceLocator()->get('Entity\User')->getCurrentUser()['id'];
        foreach ($sourceVehiclesIds as $id) {
            $data[] = [
                'vehicle' => $id,
                'licence' => $targetLicenceId,
                'specifiedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
                'createdBy' => $currentUserId,
                'lastModifiedBy' => $currentUserId
            ];
        }
        return $this->getServiceLocator()
            ->get('Entity\LicenceVehicle')
            ->multiCreate($data)['id'];
    }

    /**
     * Remove goods discs
     *
     * @param array $sourceLicence
     * @param array $sourceLicenceVehicles
     */
    protected function removeGoodsDiscs($sourceLicence, $sourceLicenceVehicles)
    {
        $discsToCease = [];
        foreach ($sourceLicence['licenceVehicles'] as $lv) {
            if (array_search($lv['id'], $sourceLicenceVehicles) !== false) {
                foreach ($lv['goodsDiscs'] as $gd) {
                    $discsToCease[] = $gd['id'];
                }
            }
        }
        $this->getServiceLocator()
            ->get('Entity\GoodsDisc')
            ->ceaseDiscs($discsToCease);
    }

    /**
     * Create goods discs
     *
     * @param array $targetLicenceVehicles
     */
    protected function createGoodsDiscs($targetLicenceVehicles)
    {
        $licenceVehicles = [];
        foreach ($targetLicenceVehicles as $id) {
            $licenceVehicles[] = ['id' => $id];
        }
        $this->getServiceLocator()
            ->get('Entity\GoodsDisc')
            ->createForVehicles($licenceVehicles);
    }
}
