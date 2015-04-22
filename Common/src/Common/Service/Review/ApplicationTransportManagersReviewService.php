<?php

/**
 * Application TransportManagers Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Application TransportManagers Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTransportManagersReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $tmService = $this->getServiceLocator()->get('Review\TransportManagers');

        return [
            'subSections' => [
                [
                    'mainItems' => $tmService->getConfigFromData($data['transportManagers'])
                ]
            ]
        ];
    }
}
