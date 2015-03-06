<?php

/**
 * Application People Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Application People Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPeopleReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = [])
    {
        $mainItems = [];

        $people = array_merge(
            $data['applicationOrganisationPersons'],
            $data['licence']['organisation']['organisationPersons']
        );

        $peopleService = $this->getServiceLocator()->get('Review\People');

        $showPosition = $peopleService->shouldShowPosition($data);

        foreach ($people as $person) {
            $mainItems[] = $peopleService->getConfigFromData($person, $showPosition);
        }

        return [
            'subSections' => [
                [
                    'mainItems' => $mainItems
                ]
            ]
        ];
    }
}
