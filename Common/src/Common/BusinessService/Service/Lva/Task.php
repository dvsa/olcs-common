<?php

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\Response;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Task implements
    BusinessServiceInterface,
    BusinessRuleAwareInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use BusinessServiceAwareTrait,
        BusinessRuleAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $assignmentData = [
            'category' => $params['category']
        ];

        $data = $this->getBusinessRuleManager()->get('Task')->validate($params);

        $assignment = $this->getServiceLocator()->get('Processing\Task')->getAssignment($assignmentData);

        $saveData = array_merge($data, $assignment);

        $saved = $this->getServiceLocator()->get('Entity\Task')->save($saveData);

        $response = new Response();
        $response->setType(Response::TYPE_PERSIST_SUCCESS);
        $response->setData(['id' => $saved['id']]);

        return $response;
    }
}
