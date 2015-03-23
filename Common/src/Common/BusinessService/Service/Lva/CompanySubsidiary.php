<?php

/**
 * Company Subsidiary
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
 * Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompanySubsidiary implements
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
        $id = $params['id'];
        $data = $params['data'];
        $licenceId = $params['licenceId'];
        $data['licence'] = $licenceId;

        // If we have added, or have changed a company subsidiary
        if ($id === null || $this->hasChangedSubsidiaryCompany($id, $data)) {

            $taskParams = [
                'action' => $id === null ? 'added' : 'updated',
                'name' => $data['name'],
                'licenceId' => $licenceId
            ];

            $response = $this->getBusinessServiceManager()->get('Lva\CompanySubsidiaryChangeTask')
                ->process($taskParams);

            if (!$response->isOk()) {
                return $response;
            }
        }

        $this->getServiceLocator()->get('Entity\CompanySubsidiary')->save($data);

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);

        return $response;
    }

    protected function hasChangedSubsidiaryCompany($id, $data)
    {
        return $this->getServiceLocator()->get('Entity\Organisation')->hasChangedSubsidiaryCompany($id, $data);
    }
}
