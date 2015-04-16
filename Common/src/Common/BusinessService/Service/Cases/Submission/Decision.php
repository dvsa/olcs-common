<?php

/**
 * Decision
 */
namespace Common\BusinessService\Service\Cases\Submission;

use Common\BusinessService\Service\Cases\Submission\SubmissionAction;
use Common\Service\Data\CategoryDataService;

/**
 * Decision
 */
class Decision extends SubmissionAction
{
    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $params['subCategory'] = CategoryDataService::TASK_SUB_CATEGORY_DECISION;

        return parent::process($params);
    }
}
