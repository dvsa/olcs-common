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

        $config = [
            'subSections' => [
                [
                    'mainItems' => [
                        [
                            'multiItems' => [
                                $this->getCompanyNamePartial($organisation),
                                $this->getTradingNamePartial($organisation),
                                $this->getNatureOfBusinessPartial($organisation)
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if ($this->isLtdOrLlp) {
            $config['subSections'][0]['mainItems'][0]['multiItems'][] = $this->getRegisteredAddressPartial($organisation);
            $config['subSections'][0]['mainItems'][] = $this->getSubsidiaryCompaniesPartial($data);
        }

        return $config;
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

    /**
     * @NOTE I think the organisationNatureOfBusiness table should be a straight many-to-many so this could change
     *
     * @param array $data
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
        return [
            [
                'label' => 'application-review-business-details-registered-address',
                'value' => $this->formatFullAddress($data['contactDetails']['address'])
            ]
        ];
    }

    /**
     * @NOTE I think the companySubsidiaryLicence table should be a straight many-to-many so this could change
     *
     * @param array $data
     */
    protected function getSubsidiaryCompaniesPartial($data)
    {
        $companySubsidiaries = $data['licence']['companySubsidiaries'];

        $config = ['header' => 'application-review-business-details-subsidiary-company-header'];

        if (empty($companySubsidiaries)) {
            $config['freetext'] = $this->translate('review-none-added');

            return $config;
        }

        $config['multiItems'] = [];

        foreach ($companySubsidiaries as $companySubsidiaryLink) {
            $item = [
                [
                    'label' => 'application-review-business-details-subsidiary-company-name',
                    'value' => $companySubsidiaryLink['companySubsidiary']['name']
                ],
                [
                    'label' => 'application-review-business-details-subsidiary-company-no',
                    'value' => $companySubsidiaryLink['companySubsidiary']['companyNo']
                ]
            ];

            $config['multiItems'][] = $item;
        }

        return $config;
    }
}
