<?php

/**
 * Submission
 */
namespace Common\BusinessService\Service\Cases\Submission;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Data\CategoryDataService;


/**
 * SubmissionAction
 */
class Submission implements BusinessServiceInterface, BusinessServiceAwareInterface, ServiceLocatorAwareInterface
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
        $returnData = $this->getServiceLocator()->get('Entity\Submission')->save($params['data']);

        $submissionId = isset($params['data']['id']) ? $params['data']['id'] : $returnData['id'];

        $submissionService = $this->getServiceLocator()->get('Olcs\Service\Data\Submission');
        $submission = $submissionService->fetchData($submissionId);

        if (!empty($params['data']['recipientUser'])) {
            $taskParams = [
                'caseId' => $submission['caseId'],
                'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_ASSIGNMENT,
                'submissionId' => $submission['id'],
                'recipientUser' => $params['data']['recipientUser'],
                'urgent' => $params['data']['urgent'],
            ];
            $response = $this->getBusinessServiceManager()->get('Cases\Submission\SubmissionAssignmentTask')->process
                ($taskParams);

            if (!$response->isOk()) {
                return $response;
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}
