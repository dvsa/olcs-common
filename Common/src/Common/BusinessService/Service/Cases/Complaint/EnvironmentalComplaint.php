<?php

/**
 * Environmental Complaint
 */
namespace Common\BusinessService\Service\Cases\Complaint;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Common\Service\Data\CategoryDataService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Environmental Complaint
 */
class EnvironmentalComplaint implements BusinessServiceInterface, BusinessServiceAwareInterface, ServiceLocatorAwareInterface
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
        if (empty($params['id'])) {
            // create a task
            $this->createTask(
                [
                    'caseId' => $params['caseId'],
                ]
            );
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }

    /**
     * Create a task
     *
     * @param array $params
     */
    private function createTask(array $params)
    {
        // get the licence for the case
        $case = $this->getServiceLocator()->get('DataServiceManager')->get('Olcs\Service\Data\Cases')
            ->fetchData(
                $params['caseId'],
                ['children' => ['licence']]
            );

        // get details of the recipient and the current user
        $currentUser = $this->getServiceLocator()->get('Entity\User')->getCurrentUser();
        $recipientUser = $currentUser;

        // set the task details
        $taskParams = [
            'category' => CategoryDataService::CATEGORY_ENVIRONMENTAL,
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_REVIEW_COMPLAINT,
            'description' => 'Review complaint',
            'actionDate' => $case['licence']['reviewDate'],
            'assignedToUser' => $recipientUser['id'],
            'assignedToTeam' => $recipientUser['team']['id'],
            'isClosed' => 'N',
            'urgent' => 'N',
            'assignedByUser' => $currentUser['id'],
            'case' => $params['caseId'],
        ];

        return $this->getBusinessServiceManager()->get('Task')->process($taskParams);
    }
}
