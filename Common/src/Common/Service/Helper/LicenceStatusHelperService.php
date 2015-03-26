<?php

/**
 * LicenceStatusHelperService.php
 */

namespace Common\Service\Helper;

use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\LicenceStatusRuleEntityService;

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
     * Is the licence curtailable.
     *
     * @param null $licenceId The licence id.
     *
     * @return bool
     */
    public function isLicenceCurtailable($licenceId = null)
    {
        return !($this->isLicenceActive($licenceId, true));
    }

    public function isLicenceSuspendable($licenceId = null)
    {
        return !($this->isLicenceActive($licenceId, true));
    }

    public function isLicenceRevokeable($licenceId = null)
    {
        return !($this->isLicenceActive($licenceId, true));
    }

    /**
     * Is the licence currently 'active' as defined by OLCS-8071
     * (https://jira.i-env.net/browse/OLCS-8071)
     *
     * @param null $licenceId The licence id.
     * @param bool $returnBool Whether the answer the question true or false.
     *
     * @return array|bool
     */
    private function isLicenceActive($licenceId = null, $returnBool = false)
    {
        if (is_null($licenceId)) {
            throw new \InvalidArgumentException(__METHOD__ . ' expects a valid licence id.');
        }

        $result = array();
        $result['communityLicences'] = $this->hasActiveCommunityLicences($licenceId);
        $result['busRoutes'] = $this->hasActiveBusRoutes($licenceId);
        $result['consideringVariations'] = $this->hasVariationsUnderConsideration($licenceId);

        $this->messages = $result;

        if ($returnBool) {
            // if any of the criteria are of an array type assume that the licence is active.
            foreach ($result as $key => $criteria) {
                if (is_array($criteria)) {
                    return true;
                }
            }

            return false;
        }

        return $result;
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

        foreach ($busRoutes['Results'] as $route) {
            switch($route['busRegStatus']) {
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

        foreach ($variantApplications['Results'] as $key => $application) {
            if ($application['isVariation']) {
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
     * Alias function to enable to abstraction of curtailing a licence with immediate effect.
     *
     * @param $licenceId The licence id.
     *
     * @return void
     */
    public function curtailNow($licenceId)
    {
        $licenceStatusEntityService = $this->getServiceLocator()->get('Entity\LicenceStatusRule');
        $licenceEntityService = $this->getServiceLocator()->get('Entity\Licence');

        $currentLicenceCurtailments = $licenceStatusEntityService->getStatusesForLicence(
            $licenceId,
            array(
                'licenceStatus' => LicenceStatusRuleEntityService::LICENCE_STATUS_RULE_CURTAILED
            )
        );

        if ((int)$currentLicenceCurtailments['Count'] > 0) {
            foreach ($currentLicenceCurtailments['Results'] as $curtailment) {
                $licenceStatusEntityService->removeStatusesForLicence($curtailment['id']);
            }
        }

        $licenceEntityService->forceUpdate(
            $licenceId,
            array('status' => LicenceEntityService::LICENCE_STATUS_CURTAILED)
        );
    }
}
