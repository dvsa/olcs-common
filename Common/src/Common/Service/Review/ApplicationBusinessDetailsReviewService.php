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
    private $isLtdOrLlp;

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $organisation = $data['licence']['organisation'];

        $this->isLtdOrLlp = in_array(
            $organisation['type']['id'],
            [
                OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY,
                OrganisationEntityService::ORG_TYPE_LLP
            ]
        );

        $mainItems = [
            [
                'multiItems' => [
                    $this->getCompanyNamePartial($organisation),
                    $this->getTradingNamePartial($organisation),
                    $this->getNatureOfBusinessPartial($organisation)
                ]
            ]
        ];

        if ($this->isLtdOrLlp) {
            $mainItems[0]['multiItems'][] = $this->getRegisteredAddressPartial($organisation);
            $mainItems[] = $this->getSubsidiaryCompaniesPartial($data);
        }

        return ['subSections' => [['mainItems' => $mainItems]]];
    }

    protected function getCompanyNamePartial($data)
    {
        if ($this->isLtdOrLlp) {
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

    protected function getNatureOfBusinessPartial($data)
    {
        $list = [];
        $first = true;

        foreach ($data['natureOfBusinesses'] as $natureOfBusinessLink) {
            $label = '';
            if ($first) {
                $label = 'application-review-business-details-nature-of-business';
                $first = false;
            }
            $list[] = [
                'label' => $label,
                'value' => $this->formatRefData($natureOfBusinessLink)
            ];
        }

        return $list;
    }

    protected function getRegisteredAddressPartial($data)
    {
        return [
            [
                'label' => 'application-review-business-details-registered-address',
                'value' => $this->formatFullAddress($data['contactDetails']['address'])
            ]
        ];
    }

    protected function getSubsidiaryCompaniesPartial($data)
    {
        $companySubsidiaries = $data['licence']['companySubsidiaries'];

        $config = ['header' => 'application-review-business-details-subsidiary-company-header'];

        if (empty($companySubsidiaries)) {
            $config['freetext'] = $this->translate('review-none-added');

            return $config;
        }

        $config['multiItems'] = [];

        foreach ($companySubsidiaries as $companySubsidiary) {
            $item = [
                [
                    'label' => 'application-review-business-details-subsidiary-company-name',
                    'value' => $companySubsidiary['name']
                ],
                [
                    'label' => 'application-review-business-details-subsidiary-company-no',
                    'value' => $companySubsidiary['companyNo']
                ]
            ];

            $config['multiItems'][] = $item;
        }

        return $config;
    }
}
