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
        $this->getServiceLocator()->get('Entity\Submission')->save($params['data']);

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}
