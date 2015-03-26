<?php

/**
 * Company Subsidiary Change Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\Service\Data\CategoryDataService;

/**
 * Company Subsidiary Change Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompanySubsidiaryChangeTask implements BusinessServiceInterface, BusinessServiceAwareInterface
{
    use BusinessServiceAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $taskParams = [
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
            'description' => 'Subsidiary company ' . $params['action'] . ' - ' . $params['name'],
            'licence' => $params['licenceId']

        ];

        return $this->getBusinessServiceManager()->get('Lva\Task')->process($taskParams);
    }
}
