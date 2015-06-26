<?php

/**
 * Application Addresses Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\PhoneContactEntityService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Application Addresses Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationAddressesReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $phoneContacts = $data['licence']['correspondenceCd']['phoneContacts'];

        $config = [
            'subSections' => [
                [
                    'mainItems' => [
                        [
                            'header' => 'application-review-addresses-correspondence-title',
                            'multiItems' => [
                                [
                                    [
                                        'label' => 'application-review-addresses-fao',
                                        'value' => $data['licence']['correspondenceCd']['fao']
                                    ],
                                    [
                                        'label' => 'application-review-addresses-correspondence-address',
                                        'value' => $this->formatFullAddress(
                                            $data['licence']['correspondenceCd']['address']
                                        )
                                    ]
                                ]
                            ]
                        ],
                        [
                            'header' => 'application-review-addresses-contact-details-title',
                            'multiItems' => [
                                [
                                    [
                                        'label' => 'application-review-addresses-correspondence-business',
                                        'value' => $this->getPhoneNumber(
                                            $phoneContacts,
                                            PhoneContactEntityService::TYPE_BUSINESS
                                        )
                                    ],
                                    [
                                        'label' => 'application-review-addresses-correspondence-home',
                                        'value' => $this->getPhoneNumber(
                                            $phoneContacts,
                                            PhoneContactEntityService::TYPE_HOME
                                        )
                                    ],
                                    [
                                        'label' => 'application-review-addresses-correspondence-mobile',
                                        'value' => $this->getPhoneNumber(
                                            $phoneContacts,
                                            PhoneContactEntityService::TYPE_MOBILE
                                        )
                                    ],
                                    [
                                        'label' => 'application-review-addresses-correspondence-fax',
                                        'value' => $this->getPhoneNumber(
                                            $phoneContacts,
                                            PhoneContactEntityService::TYPE_FAX
                                        )
                                    ],
                                    [
                                        'label' => 'application-review-addresses-correspondence-email',
                                        'value' => $data['licence']['correspondenceCd']['emailAddress']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $showEstablishmentAddress = in_array(
            $data['licenceType']['id'],
            [
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            ]
        );

        if ($showEstablishmentAddress) {
            $config['subSections'][0]['mainItems'][] = [
                'header' => 'application-review-addresses-establishment-title',
                'multiItems' => [
                    [
                        [
                            'label' => 'application-review-addresses-establishment-address',
                            'value' => $this->formatFullAddress($data['licence']['establishmentCd']['address'])
                        ]
                    ]
                ]
            ];
        }

        return $config;
    }

    private function getPhoneNumber($phoneContacts, $which)
    {
        if (is_array($phoneContacts)) {
            foreach ($phoneContacts as $phoneContact) {
                if ($phoneContact['phoneContactType']['id'] === $which) {
                    return $phoneContact['phoneNumber'];
                }
            }
        }

        return '';
    }
}
