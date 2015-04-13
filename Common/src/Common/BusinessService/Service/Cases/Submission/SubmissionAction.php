<?php

/**
 * SubmissionAction
 */
namespace Common\BusinessService\Service\Cases\Submission;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * SubmissionAction
 */
class SubmissionAction implements BusinessServiceInterface, BusinessServiceAwareInterface, ServiceLocatorAwareInterface
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
        $this->getServiceLocator()->get('Entity\SubmissionAction')->save($params['data']);

        if (empty($params['id'])) {
            // create a submission task
            $response = $this->getBusinessServiceManager()->get('Cases\Submission\SubmissionActionTask')
                ->process(
                    [
                        'submissionId' => $params['submissionId'],
                        'caseId' => $params['caseId'],
                        'subCategory' => $params['subCategory'],
                        'urgent' => $params['data']['urgent'],
                        'submissionActionStatus' => $params['data']['submissionActionStatus'],
                        'recipientUser' => $params['data']['recipientUser'],
                    ]
                );

            if (!$response->isOk()) {
                return $response;
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}
