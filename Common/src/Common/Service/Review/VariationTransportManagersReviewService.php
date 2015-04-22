<?php

/**
 * Variation TransportManagers Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Variation TransportManagers Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTransportManagersReviewService extends AbstractReviewService
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

        $actions = [];

        foreach ($data['transportManagers'] as $transportManagerApplication) {

            $actions[$transportManagerApplication['action']][] = $transportManagerApplication;
        }

        $subSections = [];

        if (isset($actions['A'])) {

            $subSections[] = [
                'title' => 'review-transport-manager-added-title',
                'mainItems' => $tmService->getConfigFromData($actions['A'])
            ];
        }

        if (isset($actions['U'])) {

            $subSections[] = [
                'title' => 'review-transport-manager-updated-title',
                'mainItems' => $tmService->getConfigFromData($actions['U'])
            ];
        }

        if (isset($actions['D'])) {

            $subSections[] = [
                'title' => 'review-transport-manager-deleted-title',
                'mainItems' => $tmService->getConfigFromData($actions['D'])
            ];
        }

        return [
            'subSections' => $subSections
        ];
    }
}
