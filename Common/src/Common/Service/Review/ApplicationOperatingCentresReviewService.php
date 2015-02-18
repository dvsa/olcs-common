<?php

/**
 * Application Operating Centres Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Application Operating Centres Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentresReviewService extends AbstractReviewService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $config = [
            'subSections' => [
                [
                    'mainItems' => [

                    ]
                ]
            ]
        ];

        $isPsv = $this->isPsv($data);

        if ($isPsv) {
            $ocService = $this->getServiceLocator()->get('Review\PsvOperatingCentre');
            $authService = $this->getServiceLocator()->get('Review\PsvOcTotalAuth');
        } else {
            $ocService = $this->getServiceLocator()->get('Review\GoodsOperatingCentre');
            $authService = $this->getServiceLocator()->get('Review\GoodsOcTotalAuth');
        }

        foreach ($data['operatingCentres'] as $operatingCentre) {
            $config['subSections'][0]['mainItems'][] = $ocService->getConfigFromData($operatingCentre);
        }

        $config['subSections'][0]['mainItems'][] = $this->formatTrafficArea($data);

        $config['subSections'][0]['mainItems'][] = $authService->getConfigFromData($data);

        return $config;
    }

    private function formatTrafficArea($data)
    {
        return [
            'header' => 'review-operating-centres-traffic-area-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-traffic-area',
                        'value' => $data['licence']['trafficArea']['name']
                    ]
                ]
            ]
        ];
    }
}
