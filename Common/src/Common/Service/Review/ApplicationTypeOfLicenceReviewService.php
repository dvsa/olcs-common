<?php

/**
 * Application Type Of Licence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\LicenceEntityService;

/**
 * Application Type Of Licence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTypeOfLicenceReviewService implements ReviewServiceInterface
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $config = [
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-type-of-licence-operator-location',
                        'value' => $this->getOperatorLocation($data)
                    ]
                ],
                [
                    [
                        'label' => 'application-review-type-of-licence-licence-type',
                        'value' => $this->getLicenceType($data)
                    ]
                ]
            ]
        ];

        // We only show operator type for GB, as NI is always goods
        if ($data['niFlag'] === 'N') {
            $config['multiItems'][0][] = [
                'label' => 'application-review-type-of-licence-operator-type',
                'value' => $this->getOperatorType($data)
            ];
        }

        return $config;
    }

    protected function getOperatorLocation($data)
    {
        return $data['niFlag'] === 'N' ? 'Great Britain' : 'Northern Ireland';
    }

    protected function getOperatorType($data)
    {
        return $data['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE ? 'Goods' : 'PSV';
    }

    protected function getLicenceType($data)
    {
        return $data['licenceType']['description'];
    }
}
