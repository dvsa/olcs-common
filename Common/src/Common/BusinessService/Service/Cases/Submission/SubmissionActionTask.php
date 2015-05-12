<?php

/**
 * Submission Action Task
 */
namespace Common\BusinessService\Service\Cases\Submission;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\Service\Data\CategoryDataService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Submission Action Task
 */
class SubmissionActionTask implements
    BusinessServiceInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use BusinessServiceAwareTrait;
    use ServiceLocatorAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        // get the licence id for the case
        $case = $this->getServiceLocator()->get('DataServiceManager')->get('Olcs\Service\Data\Cases')
            ->fetchData(
                $params['caseId'],
                ['children' => ['licence']]
            );

        // get details of the recipient and the current user
        $userService = $this->getServiceLocator()->get('Entity\User');
        $recipientUser = $userService->getUserDetails($params['recipientUser']);
        $currentUser = $userService->getCurrentUser();

        // get current date
        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();

        $translatedActionTypeString = $this->generateActionTypeString($params['actionTypes']);

        // set task description
        switch ($params['subCategory']) {
            case CategoryDataService::TASK_SUB_CATEGORY_DECISION:
                $subCategoryDescription = 'Decision:';
                break;
            case CategoryDataService::TASK_SUB_CATEGORY_RECOMMENDATION:
            default:
                $subCategoryDescription = 'Recommendations:';
                break;
        }

        $description = sprintf(
            'Licence %s Case %s Submission %s %s %s',
            $case['licence']['id'], $params['caseId'], $params['submissionId'],
            $subCategoryDescription, $translatedActionTypeString
        );

        // set the task details
        $taskParams = [
            'category' => CategoryDataService::CATEGORY_SUBMISSION,
            'subCategory' => $params['subCategory'],
            'description' => $description,
            'actionDate' => $date,
            'assignedToUser' => $recipientUser['id'],
            'assignedToTeam' => $recipientUser['team']['id'],
            'isClosed' => 'N',
            'urgent' => $params['urgent'],
            'assignedByUser' => $currentUser['id'],
            'case' => $params['caseId'],
        ];

        return $this->getBusinessServiceManager()->get('Task')->process($taskParams);
    }

    /**
     * Generate the action type string (either a list of recommendations or a single decision).
     *
     * @param array|string $actionTypes
     * @return string
     */
    private function generateActionTypeString($actionTypes)
    {
        $translatedActionTypeString = '';

        // process supports both arrays and single values for recommendations (1:n) and decisions (1:1) respectively
        if (is_array($actionTypes)) {
            // translate submission action types
            foreach ($actionTypes as $actionType) {
                $translatedActionTypeString
                    .= $this->getServiceLocator()->get('DataServiceManager')->get('\Common\Service\Data\RefData')
                        ->getDescription($actionType) . ', ';
            }
            $translatedActionTypeString = substr($translatedActionTypeString, 0, -2);
        } else {
            $translatedActionTypeString = $this->getServiceLocator()->get('DataServiceManager')->get
                ('\Common\Service\Data\RefData')
                ->getDescription($actionTypes);
        }

        return $translatedActionTypeString;
    }
}
