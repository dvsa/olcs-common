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

        // translate submission action status
        $translatedActionStatus
            = $this->getServiceLocator()->get('DataServiceManager')->get('\Common\Service\Data\RefData')
                ->getDescription($params['submissionActionStatus']);

        // set task description
        switch ($params['subCategory']) {
            case CategoryDataService::TASK_SUB_CATEGORY_DECISION:
                $subCategoryDescription = 'Decision';
                break;
            case CategoryDataService::TASK_SUB_CATEGORY_RECOMMENDATION:
            default:
                $subCategoryDescription = 'Recommendation';
                break;
        }

        $description = sprintf(
            'Licence %s Case %s Submission %s %s %s',
            $case['licence']['id'], $params['caseId'], $params['submissionId'],
            $subCategoryDescription, $translatedActionStatus
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
}
