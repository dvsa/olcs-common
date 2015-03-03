<?php

/**
 * Application Business Details Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\OrganisationEntityService;

/**
 * Application Business Details Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationBusinessDetailsReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $organisation = $data['licence']['organisation'];

        $config = [
            'multiItems' => [
                $this->getCompanyNamePartial($organisation),
                $this->getTradingNamePartial($organisation),
                $this->getNatureOfBusinessPartial($organisation)
            ]
        ];

        // If Ltd/LLP
        if (true) {
            $config['multiItems'][] = $this->getRegisteredAddressPartial($organisation);
            $config['multiItems'][] = $this->getSubsidiaryCompaniesPartial($organisation);
        }

        return $config;
    }

    protected function getCompanyNamePartial($data)
    {
        $isLtdOrLlp = in_array(
            $data['type']['id'],
            [
                OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY,
                OrganisationEntityService::ORG_TYPE_LLP
            ]
        );

        if ($isLtdOrLlp) {
            return [
                [
                    'label' => 'application-review-business-details-company-no',
                    'value' => $data['companyOrLlpNo']
                ],
                [
                    'label' => 'application-review-business-details-company-name',
                    'value' => $data['name']
                ]
            ];
        }

        if ($data['type']['id'] === OrganisationEntityService::ORG_TYPE_PARTNERSHIP) {
            return [
                [
                    'label' => 'application-review-business-details-partnership-name',
                    'value' => $data['name']
                ]
            ];
        }

        if ($data['type']['id'] === OrganisationEntityService::ORG_TYPE_OTHER) {
            return [
                [
                    'label' => 'application-review-business-details-organisation-name',
                    'value' => $data['name']
                ]
            ];
        }
    }

    protected function getTradingNamePartial($data)
    {
        if ($data['type']['id'] === OrganisationEntityService::ORG_TYPE_OTHER) {
            return;
        }

        if (empty($data['tradingNames'])) {
            return [
                [
                    'label' => 'application-review-business-details-trading-names',
                    'value' => $this->translate('review-none-added')
                ]
            ];
        }

        $tradingNamesList = [];

        $first = true;

        foreach ($data['tradingNames'] as $tradingName) {
            $label = '';
            if ($first) {
                $label = 'application-review-business-details-trading-names';
                $first = false;
            }
            $tradingNamesList[] = [
                'label' => $label,
                'value' => $tradingName['name']
            ];
        }

        return $tradingNamesList;
    }

    /**
     * @NOTE I think the organisationNatureOfBusiness table should be a straight many-to-many so this could change
     *
     * @param type $data
     */
    protected function getNatureOfBusinessPartial($data)
    {
        $list = [];
        $first = true;

        foreach ($data['natureOfBusinesss'] as $natureOfBusinessLink) {
            $label = '';
            if ($first) {
                $label = 'application-review-business-details-nature-of-business';
                $first = false;
            }
            $list[] = [
                'label' => $label,
                'value' => $this->formatRefData($natureOfBusinessLink['refData'])
            ];
        }

        return $list;
    }

    protected function getRegisteredAddressPartial($data)
    {

    }

    protected function getSubsidiaryCompaniesPartial($data)
    {

    }
}
