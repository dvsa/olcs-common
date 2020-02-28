<?php

/**
 * LicenceStatusHelperService.php
 */

namespace Common\Service\Helper;

use Common\RefData;

/**
 * Class LicenceStatusHelperService
 *
 * This helper provides business logic around validation (outside of forms) for licence actions.
 *
 * @package Common\Service\Helper
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceStatusHelperService extends AbstractHelperService
{
    protected $messages = array();

    /**
     * Get the validation messages.
     *
     * @return array The messages
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Is the licence currently 'active' as defined by OLCS-8071
     * (https://jira.i-env.net/browse/OLCS-8071)
     *
     * @param null $licenceId The licence id.
     *
     * @return boolean
     */
    public function isLicenceActive($licenceId = null)
    {
        if (is_null($licenceId)) {
            throw new \InvalidArgumentException(__METHOD__ . ' expects a valid licence id.');
        }

        $result = array();
        $result['communityLicences'] = $this->hasActiveCommunityLicences($licenceId);
        $result['busRoutes'] = $this->hasActiveBusRoutes($licenceId);
        $result['consideringVariations'] = $this->hasVariationsUnderConsideration($licenceId);

        $this->messages = $result;

        // return true or false, messages can be retrieved later via getMessages()
        foreach ($result as $value) {
            if ($value !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Does this licence have active community licences against it.
     *
     * @param null $licenceId The licence id
     *
     * @return array|bool
     */
    protected function hasActiveCommunityLicences($licenceId = null)
    {
        $comLicenceEntityService = $this->getServiceLocator()
            ->get('Entity\CommunityLic');

        $comLicences = $comLicenceEntityService->getValidLicencesForLicenceStatus($licenceId);

        if ($comLicences['Count'] !== 0) {
            return $this->createMessage('There are active, pending or suspended community licences');
        }

        return false;
    }

    /**
     * Check if there are active bus routes on the licence.
     *
     * @param null $licenceId The licence id.
     *
     * @return array|bool
     */
    protected function hasActiveBusRoutes($licenceId = null)
    {
        $busRegEntityService = $this->getServiceLocator()
            ->get('Entity\BusReg');

        $busRoutes = $busRegEntityService->findByLicenceId($licenceId);

        if (!$busRoutes) {
            return false;
        }

        foreach ($busRoutes['Results'] as $route) {
            switch ($route['busRegStatus']) {
                case "New":
                case "Registered":
                case "Variation":
                case "Cancellation":
                    return $this->createMessage('There are active bus routes on this licence');
            }
        }

        return false;
    }

    /**
     * Check if a licence still has active variations under consideration against it.
     *
     * @param null|int $licenceId The licence identifier to search against.
     *
     * @return bool|array
     */
    protected function hasVariationsUnderConsideration($licenceId = null)
    {
        $applicationEntityService = $this->getServiceLocator()
            ->get('Entity\Application');

        $variantApplications = $applicationEntityService->getApplicationsForLicence($licenceId);

        foreach ($variantApplications['Results'] as $application) {
            if ($application['isVariation']
                && $application['status']['id'] == RefData::APPLICATION_STATUS_UNDER_CONSIDERATION
            ) {
                return $this->createMessage('There are applications still under consideration');
            }
        }

        return false;
    }

    /**
     * Return an error message array for display.
     *
     * @param null|string $message The error message.
     *
     * @return array
     */
    private function createMessage($message = null)
    {
        return array(
            'result' => true,
            'message' => $message
        );
    }

    /**
     * Enables the abstraction of curtailing a licence with immediate effect.
     *
     * @param $licenceId The licence id.
     *
     * @return void
     */
    public function curtailNow($licenceId)
    {
        $this->removeStatusRulesByLicenceAndType(
            $licenceId,
            RefData::LICENCE_STATUS_RULE_CURTAILED
        );

        $licenceEntityService = $this->getServiceLocator()->get('Entity\Licence');

        $saveData = array(
            'status' => RefData::LICENCE_STATUS_CURTAILED,
            'curtailedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
        );

        return $licenceEntityService->forceUpdate($licenceId, $saveData);
    }

    /**
     * Enables the abstraction of revoking a licence with immediate effect.
     *
     * @param $licenceId The licence id.
     *
     * @return void
     */
    public function revokeNow($licenceId)
    {
        $this->removeStatusRulesByLicenceAndType(
            $licenceId,
            RefData::LICENCE_STATUS_RULE_REVOKED
        );

        $licenceEntityService = $this->getServiceLocator()->get('Entity\Licence');
        $revocationData = $licenceEntityService->getRevocationDataForLicence($licenceId);

        $this->ceaseDiscs($revocationData);
        $this->removeLicenceVehicles($revocationData['licenceVehicles']);
        $this->removeTransportManagers($revocationData['tmLicences']);

        $saveData = array(
            'status' => RefData::LICENCE_STATUS_REVOKED,
            'revokedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
        );

        return $licenceEntityService->forceUpdate($licenceId, $saveData);
    }

    /**
     * Enables the abstraction of suspending a licence with immediate effect.
     *
     * @param $licenceId The licence id.
     *
     * @return void
     */
    public function suspendNow($licenceId)
    {
        $licenceEntityService = $this->getServiceLocator()->get('Entity\Licence');

        $this->removeStatusRulesByLicenceAndType(
            $licenceId,
            RefData::LICENCE_STATUS_RULE_SUSPENDED
        );

        $saveData = array(
            'status' => RefData::LICENCE_STATUS_SUSPENDED,
            'suspendedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
        );

        return $licenceEntityService->forceUpdate($licenceId, $saveData);
    }

    /**
     * Surrender a licence with immediate effect.
     *
     * @param int $licenceId The licence id.
     * @param string $surrenderDate surrendered date, usually YYYY-mm-dd
     *
     * @return void
     */
    public function surrenderNow($licenceId, $surrenderDate)
    {
        $licenceEntityService = $this->getServiceLocator()->get('Entity\Licence');

        $surrenderData = $licenceEntityService->getRevocationDataForLicence($licenceId);

        $this->ceaseDiscs($surrenderData);
        $this->removeLicenceVehicles($surrenderData['licenceVehicles']);
        $this->removeTransportManagers($surrenderData['tmLicences']);

        $saveData = [
            'status' => RefData::LICENCE_STATUS_SURRENDERED,
            'surrenderedDate' => $surrenderDate,
        ];
        return $licenceEntityService->forceUpdate($licenceId, $saveData);
    }


    /**
     * Terminate a licence with immediate effect.
     *
     * @param $licenceId The licence id.
     * @param string $terminate terminated date, usually YYYY-mm-dd
     * (note this is actually stored as surrendered_date)
     *
     * @return void
     */
    public function terminateNow($licenceId, $terminateDate)
    {
        $licenceEntityService = $this->getServiceLocator()->get('Entity\Licence');

        $terminateData = $licenceEntityService->getRevocationDataForLicence($licenceId);

        $this->ceaseDiscs($terminateData);
        $this->removeLicenceVehicles($terminateData['licenceVehicles']);
        $this->removeTransportManagers($terminateData['tmLicences']);

        $saveData = [
            'status' => RefData::LICENCE_STATUS_TERMINATED,
            'surrenderedDate' => $terminateDate,
        ];

        return $licenceEntityService->forceUpdate($licenceId, $saveData);
    }

    /**
     * Remove the licence vehicles for a licence.
     *
     * @param null|array $licenceVehicles The licence vehicles.
     */
    public function removeLicenceVehicles($licenceVehicles = array())
    {
        $vehicles = array_map(
            function ($licenceVehicle) {
                return $licenceVehicle['id'];
            },
            $licenceVehicles
        );

        $this->getServiceLocator()->get('Entity\LicenceVehicle')->removeVehicles($vehicles);
    }

    /**
     * Remove the Transport Managers for a licence.
     *
     * @param null|array $transportManagers The TM's
     */
    public function removeTransportManagers($transportManagers = array())
    {
        $transportManagers = array_map(
            function ($transportManager) {
                return $transportManager['id'];
            },
            $transportManagers
        );

        $this->getServiceLocator()->get('Entity\TransportManagerLicence')->deleteForLicence($transportManagers);
    }

    /**
     * Helper method to cease discs based on licence data
     *
     * @param array $licenceData
     */
    public function ceaseDiscs($licenceData)
    {
        $discs = array();
        if ($licenceData['goodsOrPsv']['id'] == RefData::LICENCE_CATEGORY_PSV) {
            array_map(
                function ($disc) use (&$discs) {
                    if ($disc['ceasedDate'] === null) {
                        $discs[] = $disc['id'];
                    }
                },
                $licenceData['psvDiscs']
            );
            return $this->getServiceLocator()->get('Entity\PsvDisc')->ceaseDiscs($discs);
        } else {
            foreach ($licenceData['licenceVehicles'] as $licenceVehicle) {
                array_map(
                    function ($disc) use (&$discs) {
                        if ($disc['ceasedDate'] === null) {
                            $discs[] = $disc['id'];
                        }
                    },
                    $licenceVehicle['goodsDiscs']
                );
            }
            return $this->getServiceLocator()->get('Entity\GoodsDisc')->ceaseDiscs($discs);
        }
    }

    /**
     * Set the licence status to be valid and remove any licence status changes that would
     * regress it.
     *
     * @param $licenceId The licence id
     */
    public function resetToValid($licenceId)
    {
        $this->removeStatusRulesByLicenceAndType(
            $licenceId,
            array(
                RefData::LICENCE_STATUS_RULE_CURTAILED,
                RefData::LICENCE_STATUS_RULE_SUSPENDED,
                RefData::LICENCE_STATUS_RULE_REVOKED
            )
        );

        $licenceEntityService = $this->getServiceLocator()->get('Entity\Licence');

        $saveData = [
            'status'          => RefData::LICENCE_STATUS_VALID,
            'surrenderedDate' => null,
            'curtailedDate' => null,
            'revokedDate' => null,
            'suspendedDate' => null,
        ];

        return $licenceEntityService->forceUpdate($licenceId, $saveData);
    }

    /**
     * Remove statuses by a specific type.
     *
     * @param $licenceId The licence id
     * @param null|array|string $type The type of status to remove.
     *
     * @throws \InvalidArgumentException If licence or type aren't passed.
     */
    public function removeStatusRulesByLicenceAndType($licenceId = null, $type = null)
    {
        if (is_null($type) || is_null($licenceId)) {
            throw new \InvalidArgumentException(__METHOD__ . ' requires a licence and type.');
        }

        $licenceStatusEntityService = $this->getServiceLocator()->get('Entity\LicenceStatusRule');
        $currentLicenceStatuses = $licenceStatusEntityService->getStatusesForLicence(
            array(
                'query' => array(
                    'licence' => $licenceId,
                    'licenceStatus' => $type
                )
            )
        );

        if ((int)$currentLicenceStatuses['Count'] > 0) {
            foreach ($currentLicenceStatuses['Results'] as $status) {
                $licenceStatusEntityService->removeStatusesForLicence($status['id']);
            }
        }
    }

    /**
     * @param int $licenceId
     * @return array|null
     */
    public function getCurrentOrPendingRulesForLicence($licenceId)
    {
        // defer to generic entity service method
        $data = $this->getServiceLocator()->get('Entity\LicenceStatusRule')->getStatusesForLicence(
            [
                'query' => [
                    'licence' => $licenceId,
                    'deletedDate' => 'NULL',
                    'endProcessedDate' => 'NULL',
                ],
            ]
        );

        return $data['Count']>0 ? $data['Results'] : null;
    }

    /**
     * @param int $licenceId
     * @return boolean
     */
    public function hasQueuedRevocationCurtailmentSuspension($licenceId)
    {
        $licenceStatusEntityService = $this->getServiceLocator()->get('Entity\LicenceStatusRule');

        $data = $licenceStatusEntityService->getStatusesForLicence(
            [
                'query' => [
                    'licence' => $licenceId,
                    'deletedDate' => 'NULL',
                    'startProcessedDate' => 'NULL',
                    'licenceStatus' => [
                        RefData::LICENCE_STATUS_RULE_CURTAILED,
                        RefData::LICENCE_STATUS_RULE_SUSPENDED,
                        RefData::LICENCE_STATUS_RULE_REVOKED,
                    ],
                ],
            ]
        );

        return ((int)$data['Count'] > 0);
    }
}
