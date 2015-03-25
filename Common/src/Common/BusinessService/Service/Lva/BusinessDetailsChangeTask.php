<?php

/**
 * Business Details Change Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\Service\Data\CategoryDataService;

/**
 * Business Details Change Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetailsChangeTask implements BusinessServiceInterface, BusinessServiceAwareInterface
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
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_HEARINGS_APPEALS,
            'description' => 'Change to business details',
            'licence' => $params['licenceId']
        ];

        return $this->getBusinessServiceManager()->get('Lva\Task')->process($taskParams);
    }
}
