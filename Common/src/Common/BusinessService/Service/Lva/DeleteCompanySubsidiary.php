<?php

/**
 * Delete Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Delete Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteCompanySubsidiary implements
    BusinessServiceInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait,
        BusinessServiceAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $ids = $params['ids'];
        $licenceId = $params['licenceId'];

        $taskService = $this->getBusinessServiceManager()->get('Lva\CompanySubsidiaryChangeTask');
        $companyEntity = $this->getServiceLocator()->get('Entity\CompanySubsidiary');

        foreach ($ids as $id) {
            $company = $companyEntity->getById($id);

            $companyEntity->delete($id);

            $taskParams = [
                'action' => 'deleted',
                'name' => $company['name'],
                'licenceId' => $licenceId
            ];

            $response = $taskService->process($taskParams);

            if (!$response->isOk()) {
                return $response;
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);

        return $response;
    }
}
