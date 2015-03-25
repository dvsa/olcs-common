<?php

/**
 * Application Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Entity\LicenceEntityService as Licence;
use Common\Service\Entity\ApplicationEntityService as Application;
use Common\Service\Data\CategoryDataService as Category;

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
     * @param int $applicationId
     * @return array
     */
    public function getFormData($applicationId)
    {
        $applicationData = $this->getApplicationData($applicationId);

        $uploaded = $applicationData['financialEvidenceUploaded'];

        return [
            'id'       => $applicationId,
            'version'  => $applicationData['version'],
            'evidence' => [
                // default to Y
                'uploadNow' => is_null($uploaded) ? 'Y' : $uploaded,
            ],
        ];
    }

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
     * @param int $applicationId
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

        return $appVehicles + $otherLicenceVehicles + $otherApplicationVehicles;
    }

    /**
     * Gets the total required finance when submitting an application, taking
     * account of other pending applications and existing licences for the
     * same operator.
     *
     * @param int $applicationId
     * @return int Required finance amount
     */
    public function getRequiredFinance($applicationId)
    {
        $auths = array();

        $appData = $this->getApplicationData($applicationId);
        $appVehicles = $this->getTotalVehicleAuthForApplication($applicationId);

        // build up an array of vehicle authorisation counts/types...

        // add the application count
        $auths[] = [
            'type' => $appData['licenceType']['id'],
            'count' => $appVehicles,
            'category' => $appData['goodsOrPsv']['id'],
        ];

        // add the counts for each licence
        $licences = $this->getOtherLicences($applicationId);
        foreach ($licences as $licence) {
            $auths[] = [
                'type' => $licence['licenceType']['id'],
                'count' => $licence['totAuthVehicles'],
                'category' => $licence['goodsOrPsv']['id'],
            ];
        }

        // add the counts for each other application
        $applications = $this->getOtherApplications($applicationId);
        foreach ($applications as $application) {
            $auths[] = [
                'type' => $application['licenceType']['id'],
                'count' => $application['totAuthVehicles'],
                'category' => $application['goodsOrPsv']['id'],
            ];
        }

        return $this->getFinanceCalculation($auths);
    }

    /**
     * @param Common\Form\Form
     * @return void
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
    public function getDocuments($applicationId)
    {
        return $this->getServiceLocator()->get('Entity\Application')
            ->getDocuments(
                $applicationId,
                Category::CATEGORY_APPLICATION,
                Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
            );
    }

    /**
     * @param array $file
     * @param int $applicationId
     * @return array
     */
    public function getUploadMetaData($file, $applicationId)
    {
        $licenceId = $this->getServiceLocator()->get('Entity\Application')
            ->getLicenceIdForApplication($applicationId);

        return [
            'application' => $applicationId,
            'description' => $file['name'],
            'category'    => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL,
            'licence'     => $licenceId,
        ];
    }

    /**
     * Gets the vehicle rates to display in the help section of the page. Note
     * that currently we only display the rates according to the current
     * application category (Goods or PSV) - if the operator holds another
     * category of licence those figures will be used for the calculation but
     * are not shown.
     *
     * @param int $applicationId
     * @return array
     */
    public function getRatesForView($applicationId)
    {
        $goodsOrPsv = $this->getApplicationData($applicationId)['goodsOrPsv']['id'];

        return [
            'standardFirst' => $this->getFirstVehicleRate(
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                $goodsOrPsv
            ),
            'standardAdditional' => $this->getAdditionalVehicleRate(
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                $goodsOrPsv
            ),
            'restrictedFirst' => $this->getFirstVehicleRate(
                Licence::LICENCE_TYPE_RESTRICTED,
                $goodsOrPsv
            ),
            'restrictedAdditional' => $this->getAdditionalVehicleRate(
                Licence::LICENCE_TYPE_RESTRICTED,
                $goodsOrPsv
            ),
        ];
    }

    /**
     * Takes an array of vehicle authorisations (example below) and
     * returns the required finance amount
     *
     * array (
     *   0 =>
     *   array (
     *     'category' => 'lcat_gv'
     *     'type' => 'ltyp_si',
     *     'count' => 3,
     *   ),
     *   1 =>
     *   array (
     *     'category' => 'lcat_gv'
     *     'type' => 'ltyp_r',
     *     'count' => 3,
     *   ),
     *   2 =>
     *   array (
     *     'catgegory' => 'lcat_psv'
     *     'type' => 'ltyp_r',
     *     'count' => 1,
     *   ),
     * )
     *
     * Calculation:
     *    1 x 7000
     * +  2 x 3900
     * +  3 x 1700
     * +  1 x 2700
     * -----------
     *       22600
     *
     * @param array $auths
     * @return int
     */
    protected function getFinanceCalculation(array $auths)
    {
        $firstVehicleCharge      = 0;
        $additionalVehicleCharge = 0;
        $foundHigher             = false;
        $higherChargeTypes       = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
        ];

        // 1. Sort the array so the correct (higher) 'first vehicle' charge is
        // applied (i.e. ensure any PSV apps/licences are handled first)
        usort(
            $auths,
            function ($a, $b) {
                return $a['category'] === Licence::LICENCE_CATEGORY_PSV ? -1 : 1;
            }
        );

        // 2. Get first vehicle charge
        foreach ($auths as $key => $auth) {
            if (!$foundHigher && $count = $auth['count']>0) {
                $firstVehicleCharge = $this->getFirstVehicleRate($auth['type'], $auth['category']);
                $firstVehicleKey = $key;
            }
            if (in_array($auth['type'], $higherChargeTypes)) {
                $foundHigher = true;
            }
        }

        // 3. Ensure we don't double-count the first vehicle
        $auths[$firstVehicleKey]['count']--;

        // 4. Get the additional vehicle charges
        foreach ($auths as $key => $auth) {
            $rate = $this->getAdditionalVehicleRate($auth['type'], $auth['category']);
            $additionalVehicleCharge += ($auth['count'] * $rate);
        }

        // 5. Return the total required finance
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
            $organisationId = $this->getOrganisationId($applicationId);
            $this->otherLicences = $this->getServiceLocator()->get('Entity\Organisation')
                ->getLicencesByStatus(
                    $organisationId,
                    [
                        Licence::LICENCE_STATUS_VALID,
                        Licence::LICENCE_STATUS_SUSPENDED,
                        Licence::LICENCE_STATUS_CURTAILED,
                    ]
                );
        }
        return $this->otherLicences;
    }

    /**
     * @param int $applicationId
     * @return int
     */
    protected function getOrganisationId($applicationId)
    {
        return $this->getApplicationData($applicationId)['licence']['organisation']['id'];
    }

    /**
     * @param int $applicationId
     * @return array
     */
    protected function getOtherApplications($applicationId)
    {
        if (is_null($this->otherApplications)) {
            $this->otherApplications = [];

            $organisationId = $this->getOrganisationId($applicationId);

            $applications = $this->getServiceLocator()->get('Entity\Organisation')
                ->getNewApplicationsByStatus(
                    $organisationId,
                    [
                        Application::APPLICATION_STATUS_UNDER_CONSIDERATION,
                        Application::APPLICATION_STATUS_GRANTED,
                    ]
                );

            // filter out the current application
            $this->otherApplications = array_filter(
                $applications,
                function ($application) use ($applicationId) {
                    return $application['id'] != $applicationId;
                }
            );
        }
        return $this->otherApplications;
    }
}
