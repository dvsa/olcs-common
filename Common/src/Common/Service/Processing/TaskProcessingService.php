<?php

/**
 * Task Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Processing;

use Common\Service\Entity\TaskAllocationRules;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Task Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaskProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function getAllocationForCategory($categoryId, $data)
    {
        $category = $this->getServiceLocator()
            ->get('Entity\Category')
            ->findById($categoryId);

        if ($category === false) {
            return $this->getDefaultAllocation();
        }

        $query = $this->getQueryForRuleType($category['taskAllocationType']['id'], $data);

        if ($query === null) {
            return $this->getDefaultAllocation();
        }

        $rule = $this->getServiceLocator()
            ->get('Entity\TaskAllocationRules')
            ->findByQuery($query);

        if (!$rule/* || > 1 $rules */) {
            return $this->getDefaultAllocation();
        }

        return [
            'assignedToUser' => $rule['user']['id'],
            'assignedToTeam' => $rule['team']['id']
        ];
    }

    private function getQueryForRuleType($type, $data)
    {
        switch ($rule) {
            case TaskAllocationRules::TYPE_SIMPLE:
                return [
                    'category' => $data['category']
                ];

            // no other allocation type is yet implemented as of OLCS-3406
            case TaskAllocationRules::TYPE_MEDIUM:
            case TaskAllocationRules::TYPE_COMPLEX:
            default:
                return null;
        }
    }

    private function getDefaultAllocation()
    {
        // @TODO from 'system config' (needs clarification)
        return [
            'assignedToUser' => 123,
            'assignedToTeam' => 456
        ];
    }
}
