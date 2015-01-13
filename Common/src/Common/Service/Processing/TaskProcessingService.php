<?php

/**
 * Task Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Processing;

use Common\Service\Entity\TaskAllocationRuleEntityService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use LogicException;

/**
 * Task Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaskProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function getAssignment(array $data)
    {
        /**
         * First of all get a proper category entity based on the supplied ID
         *
         * We need to do this because it's the category which dictates the
         * allocation rule to follow; the caller doesn't choose, and nor do
         * we infer it from the array of data supplied
         */
        $category = $this->getServiceLocator()
            ->get('Entity\Category')
            ->findById($data['category']);

        if ($category === false) {
            return $this->getDefaultAllocation();
        }

        $ruleType = $category['taskAllocationType']['id'];

        /**
         * Based on the allocation type specified by the category we'll get
         * a different query which we can then just run against the
         * allocation rule entity
         */
        $query = $this->getQueryForRuleType($ruleType, $data);

        $rule = $this->getServiceLocator()
            ->get('Entity\TaskAllocationRule')
            ->findByQuery($query);

        /**
         * Multiple rules are just as useless as no rules according to AC
         */
        if ($rule === false || $rule['Count'] > 1) {
            return $this->getDefaultAllocation();
        }

        $rule = $rule['Results'][0];

        return $this->getDataForRuleAndType($ruleType, $rule);
    }

    private function getQueryForRuleType($type, $data)
    {
        switch ($type) {
            case TaskAllocationRuleEntityService::TYPE_SIMPLE:
                return [
                    'category'    => $data['category'],
                    // These NULLs are important; there will be multiple matches
                    // per category, but hopefully only one unique across cat+mlh+ta
                    'isMlh'       => 'NULL',
                    'trafficArea' => 'NULL',
                ];

            // no other allocation type is yet implemented as of OLCS-3406
            case TaskAllocationRuleEntityService::TYPE_MEDIUM:
            case TaskAllocationRuleEntityService::TYPE_COMPLEX:
            default:
                throw new LogicException('Querying for rule type "'. $type .'" is not supported');
        }
    }

    /**
     *
     */
    private function getDataForRuleAndType($type, $rule)
    {
        switch ($type) {
            case TaskAllocationRuleEntityService::TYPE_SIMPLE:
                return $this->buildDetails($rule['team']['id'], $rule['user']['id']);

            // no other allocation type is yet implemented as of OLCS-3406
            case TaskAllocationRuleEntityService::TYPE_MEDIUM:
            case TaskAllocationRuleEntityService::TYPE_COMPLEX:
            default:
                throw new LogicException('Fetching data for rule type "'. $type .'" is not supported');
        }
    }

    private function getDefaultAllocation()
    {
        $system = $this->getServiceLocator()->get('Entity\SystemParameter');

        return $this->buildDetails(
            $system->getValue('task.default_team'),
            $system->getValue('task.default_user')
        );
    }

    private function buildDetails($team, $user = null)
    {
        return [
            'assignedToTeam' => $team,
            'assignedToUser' => $user
        ];
    }
}
