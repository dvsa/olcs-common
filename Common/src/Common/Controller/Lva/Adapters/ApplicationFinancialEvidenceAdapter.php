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
    protected $applicationData = null; // cache
    protected $otherLicences = null; // cache

    /**
     * Get the total vehicle authority which includes the vehicles on this
     * application and across all other licence records where status of the
     * licence is:
     *   Under consideration
     *   Granted
     *   Valid
     *   Suspended
     *   Curtailed
     *
     * @return int
     */
    public function getTotalNumberOfAuthorisedVehicles($applicationId)
    {
        // get the total vehicle authorisation for this application
        $appVehicles = $this->getTotalVehicleAuthForApplication($applicationId);

        // get the total vehicle authorisation for other licences
        $otherVehicles = 0;
        $licences = $this->getOtherLicences($applicationId);
        foreach ($licences as $licence) {
            $otherVehicles += (int)$licence['totAuthVehicles'];
        }

        return $appVehicles + $otherVehicles;
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
                Licence::LICENCE_STATUS_UNDER_CONSIDERATION,
                Licence::LICENCE_STATUS_GRANTED,
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

        // loop thru and do teh maths
        $firstVehicleCharge      = 0;
        $additionalVehicleCharge = 0;
        $foundHigher             = false;
        $higherChargeTypes       = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
        ];
        foreach ($auths as $auths) {
            $count = $auths['count'];
            $type  = $auths['type'];
            if (!$foundHigher && $count > 0) {
                $firstVehicleCharge = $this->getFirstVehicleRate($type);
                $count--; // don't double-count the first vehicle!
            }
            if (in_array($type, $higherChargeTypes)) {
                $foundHigher = true;
            }
            $rate = $this->getAdditionalVehicleRate($type);
            $additionalVehicleCharge += ($count * $rate);
        }

        return $firstVehicleCharge + $additionalVehicleCharge;
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
    protected function getApplicationData($applicationId)
    {
        if (is_null($this->applicationData)) {
            $this->applicationData = $this->getServiceLocator()->get('Entity\Application')
                ->getDataForFinancialEvidence($applicationId);
        }
        return $this->applicationData;
    }
}
