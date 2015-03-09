<?php

/**
 * Variation People Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\OrganisationEntityService;

/**
 * Variation People Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPeopleReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = [])
    {
        if ($this->shouldShowSimpleUpgradeText($data)) {
            return ['freetext' => $this->translate('variation-review-people-change')];
        }

        $peopleService = $this->getServiceLocator()->get('Review\People');

        $peopleList = $this->splitPeopleUp($data);

        $showPosition = $peopleService->shouldShowPosition($data);

        $sections = [];

        foreach ($peopleList as $action => $people) {

            if (!empty($people)) {
                $sections[] = $this->formatSection($people, $action, $peopleService, $showPosition);
            }
        }

        return ['subSections' => $sections];
    }

    /**
     * If we are a Sole trader or partnership, we can just return a simple message
     *
     * @param array $data
     * @return boolean
     */
    private function shouldShowSimpleUpgradeText($data)
    {
        return in_array(
            $data['licence']['organisation']['type']['id'],
            [
                OrganisationEntityService::ORG_TYPE_SOLE_TRADER,
                OrganisationEntityService::ORG_TYPE_PARTNERSHIP
            ]
        );
    }

    /**
     * Split people into A, U and D lists
     *
     * @param array $data
     * @return array
     */
    private function splitPeopleUp($data)
    {
        $peopleList = ['A' => [], 'U' => [], 'D' => []];
        foreach ($data['applicationOrganisationPersons'] as $person) {
            $peopleList[$person['action']][] = $person;
        }

        return $peopleList;
    }

    private function formatSection($people, $action, $peopleService, $showPosition)
    {
        $mainItems = [];
        foreach ($people as $person) {
            $mainItems[] = $peopleService->getConfigFromData($person, $showPosition);
        }

        return [
            'title' => 'variation-review-people-' . $action . '-title',
            'mainItems' => $mainItems
        ];
    }
}
