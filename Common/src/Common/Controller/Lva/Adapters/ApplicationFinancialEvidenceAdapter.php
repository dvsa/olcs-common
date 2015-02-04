<?php

/**
 * Application Type Of Licence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Entity\LicenceEntityService as Licence;

/**
 * Application Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApplicationFinancialEvidenceAdapter extends AbstractFinancialEvidenceAdapter
{

    protected $applicationData   = null; // cache

    protected $otherLicences     = null; // cache

    protected $otherApplications = null; // cache

    /**
     * Get the total vehicle authority which includes:
     *
     *  The vehicles on this application
     *  PLUS the vehicles across other licence records with a status of:
     *   Valid
     *   Suspended
     *   Curtailed
     *  PLUS the vehicles across other new applications with a status of:
     *   Under consideration
     *   Granted
     *
     * @return int
     */
    public function getTotalNumberOfAuthorisedVehicles($applicationId)
    {
        // get the total vehicle authorisation for this application
        $appVehicles = $this->getTotalVehicleAuthForApplication($applicationId);

        // get the total vehicle authorisation for other licences
        $otherLicenceVehicles = 0;
        $licences = $this->getOtherLicences($applicationId);
        foreach ($licences as $licence) {
            $otherLicenceVehicles += (int)$licence['totAuthVehicles'];
        }

        // get the total vehicle authorisation for other applications
        // that are 'under consideration' or 'granted'
        $otherApplicationVehicles = 0;
        $applications = $this->getOtherApplications($applicationId);
        foreach ($applications as $application) {
            $otherApplicationVehicles += (int)$application['totAuthVehicles'];
        }

        return $appVehicles + $otherLicenceVehicles;
    }

    /**
     * @param int $applicationId
     * @return int Required finance amount
     */
    public function getRequiredFinance($applicationId)
    {
        $auths = array();

        $appType = $this->getApplicationData($applicationId)['licenceType']['id'];
        $appVehicles = $this->getTotalVehicleAuthForApplication($applicationId);

        // build up an array of vehicle authorisation counts/types...

        // add the application count
        $auths[] = [
            'type' => $appType,
            'count' => $appVehicles,
        ];

        // add the counts for each licence
        $licences = $this->getOtherLicences($applicationId);
        foreach ($licences as $licence) {
            $auths[] = [
                'type' => $licence['licenceType']['id'],
                'count' => $licence['totAuthVehicles'],
            ];
        }

        // add the counts for each other application
        $applications = $this->getOtherApplications($applicationId);
        foreach ($applications as $application) {
            $auths[] = [
                'type' => $application['licenceType']['id'],
                'count' => $application['totAuthVehicles'],
            ];
        }

        return $this->getFinanceCalculation($auths);
    }

    /**
     * Takes an array of vehicle authorisations (example below) and
     * returns the required finance amount
     *
     * array (
     *   0 =>
     *   array (
     *     'type' => 'ltyp_r',
     *     'count' => 3,
     *   ),
     *   1 =>
     *   array (
     *     'type' => 'ltyp_sn',
     *     'count' => 12,
     *   ),
     *   2 =>
     *   array (
     *     'type' => 'ltyp_sn',
     *     'count' => 9,
     *   ),
     *   3 =>
     *   array (
     *     'type' => 'ltyp_sn',
     *     'count' => 3,
     *   ),
     * )
     *
     * Calculation:
     *    3 x 1700
     * +  1 x 7000
     * + 11 x 3900
     * +  9 x 3900
     * +  3 x 3900
     * -----------
     *      101800
     *
     * @param array @auths
     * @return int
     */
    public function getFinanceCalculation(array $auths)
    {
        $firstVehicleCharge      = 0;
        $additionalVehicleCharge = 0;
        $foundHigher             = false;
        $higherChargeTypes       = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
        ];

        // get first vehicle charge
        foreach ($auths as $key => $auth) {
            if (!$foundHigher && $count = $auth['count']>0) {
                $firstVehicleCharge = $this->getFirstVehicleRate($auth['type']);
                $firstVehicleKey = $key;
            }
            if (in_array($auth['type'], $higherChargeTypes)) {
                $foundHigher = true;
            }
        }

        // don't double-count the first vehicle!
        $auths[$firstVehicleKey]['count']--;

        // get the additional vehicle charges
        foreach ($auths as $key => $auth) {
            $rate = $this->getAdditionalVehicleRate($auth['type']);
            $additionalVehicleCharge += ($auth['count'] * $rate);
        }

        return $firstVehicleCharge + $additionalVehicleCharge;
    }

    /**
     * @param \Zend\Form\Form $form
     */
    public function alterFormForLva($form)
    {
        $form->get('finance')->get('requiredFinance')
            ->setValue('markup-required-finance-application');
    }

    /**
     * @param int $applicationId
     * @return array
     */
    protected function getTotalVehicleAuthForApplication($applicationId)
    {
        return (int) $this->getApplicationData($applicationId)['totAuthVehicles'];
    }

    /**
     * @param int $applicationId
     * @return array
     */
    protected function getApplicationData($applicationId)
    {
        if (is_null($this->applicationData)) {
            $this->applicationData = $this->getServiceLocator()->get('Entity\Application')
                ->getDataForFinancialEvidence($applicationId);
        }
        return $this->applicationData;
    }

    /**
     * @param int $applicationId
     * @return array
     */
    protected function getOtherLicences($applicationId)
    {
        if (is_null($this->otherLicences)) {
            $organisationId = $this->getServiceLocator()->get('Entity\Application')
                ->getOrganisation($applicationId)['id'];

            $licences = $this->getServiceLocator()->get('Entity\Organisation')
                ->getLicences($organisationId);

            // filter results to valid statuses
            $validStatuses = [
                Licence::LICENCE_STATUS_VALID,
                Licence::LICENCE_STATUS_SUSPENDED,
                Licence::LICENCE_STATUS_CURTAILED,
            ];
            foreach($licences as $licence) {
                if (in_array($licence['status']['id'], $validStatuses)) {
                    $this->otherLicences[] = $licence;
                }
            }
        }
        return $this->otherLicences;
    }

    /**
     * @param int $applicationId
     * @return array
     */
    protected function getOtherApplications($applicationId)
    {
        if (is_null($this->otherApplications)) {
            $this->otherApplications = [];

            $organisationId = $this->getServiceLocator()->get('Entity\Application')
                ->getOrganisation($applicationId)['id'];

            $applications = $this->getServiceLocator()->get('Entity\Organisation')
                ->getNewApplications($organisationId);

            // filter to new applications in statuses we're interested in
            $validStatuses = [
                Licence::LICENCE_STATUS_UNDER_CONSIDERATION,
                Licence::LICENCE_STATUS_GRANTED,
            ];
            foreach($applications as $application) {
                if (in_array($application['status']['id'], $validStatuses)) {
                    $this->otherApplications[] = $application;
                }
            }
        }
        return $this->otherApplications;
    }

    /**
     * @param string $licenceType
     * @return int
     * @todo these will come from db eventually, but OLCS-2222 specifies they
     * are hard-coded for now
     */
    protected function getFirstVehicleRate($licenceType)
    {
        switch ($licenceType) {
            case Licence::LICENCE_TYPE_RESTRICTED:
                return 3100;
            default:
                // LICENCE_TYPE_SPECIAL_RESTRICTED is n/a
                return 7000;
        }
    }

    /**
     * @param string $licenceType
     * @return int
     */
    protected function getAdditionalVehicleRate($licenceType)
    {
        switch ($licenceType) {
            case Licence::LICENCE_TYPE_RESTRICTED:
                return 1700;
            default:
                // LICENCE_TYPE_SPECIAL_RESTRICTED is n/a
                return 3900;
        }
    }
}
