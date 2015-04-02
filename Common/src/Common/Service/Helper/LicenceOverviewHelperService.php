<?php

/**
 * Licence Overview Helper Service
 */
namespace Common\Service\Helper;

use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Licence Overview Helper Service
 */
class LicenceOverviewHelperService extends AbstractHelperService
{
    /**
     * Helper method to get the first trading name from licence data.
     * (Sorts trading names by createdOn date then alphabetically)
     *
     * @param array $licence licence data
     * @return string
     */
    public function getTradingNameFromLicence($licence)
    {
        if (empty($licence['organisation']['tradingNames'])) {
            return 'None';
        }

        usort(
            $licence['organisation']['tradingNames'],
            function ($a, $b) {
                if ($a['createdOn'] == $b['createdOn']) {
                    // This *should* be an extreme edge case but there is a bug
                    // in Business Details causing trading names to have the
                    // same createdOn date. Sort alphabetically to avoid
                    // 'random' behaviour.
                    return strcasecmp($a['name'], $b['name']);
                }
                return strtotime($a['createdOn']) < strtotime($b['createdOn']) ? -1 : 1;
            }
        );

        return array_shift($licence['organisation']['tradingNames'])['name'];
    }

    /**
     * Helper method to get number of current applications for the organisation
     * from licence data
     *
     * @param array $licence
     * @return int
     */
    public function getCurrentApplications($licence)
    {
        $applications = $this->getServiceLocator()->get('Entity\Organisation')->getAllApplicationsByStatus(
            $licence['organisation']['id'],
            [
                ApplicationEntityService::APPLICATION_STATUS_UNDER_CONSIDERATION,
                ApplicationEntityService::APPLICATION_STATUS_GRANTED,
            ]
        );

        return count($applications);
    }


    /**
     * Helper method to get number of community licences from licence data
     * (Standard International and PSV Restricted only, otherwise null)
     *
     * @param array $licence
     * @return int|null
     */
    public function getNumberOfCommunityLicences($licence)
    {
        $type = $licence['licenceType']['id'];
        $goodsOrPsv = $licence['goodsOrPsv']['id'];

        if ($type == LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            || ($goodsOrPsv == LicenceEntityService::LICENCE_CATEGORY_PSV
                && $type == LicenceEntityService::LICENCE_TYPE_RESTRICTED)
        ) {
            return (int) $licence['totCommunityLicences'];
        }

        return null;
    }

    /**
     * @param int $licenceId
     * @return string (count may be suffixed with '(PI)')
     */
    public function getOpenCases($licenceId)
    {
        $cases = $this->getServiceLocator()->get('Entity\Cases')
            ->getOpenForLicence($licenceId);

        $openCases = (string) count($cases);

        foreach ($cases as $c) {
            if (!empty($c['publicInquirys'])) {
                $openCases .= ' (PI)';
                break;
            }
        }

        return $openCases;
    }
}
