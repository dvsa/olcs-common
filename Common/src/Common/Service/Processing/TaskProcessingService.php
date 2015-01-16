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
use InvalidArgumentException;

/**
 * Task Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaskProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Main entrypoint; given an array of input data (which must at the
     * very least contain a category key) return the team and user to
     * whom the task should be assigned
     *
     * @param array $data
     * @return array
     */
    public function getAssignment(array $data)
    {
        $this->validateData($data);

        /**
         * First of all get a proper category entity based on the supplied ID
         *
         * We need to do this because it's the category which dictates the
         * allocation rule to follow; the caller doesn't choose, and nor do
         * we infer it from the array of data supplied (although in future
         * the data will need to already contain the relevant data for
         * medium and complex lookups)
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
        if ($rule['Count'] !== 1) {
            return $this->getDefaultAllocation();
        }

        $rule = $rule['Results'][0];

        return $this->getDataForRuleAndType($ruleType, $rule);
    }

    /**
     * Given a rule type and some data, mold that data into a suitable
     * query we can ping off to the backend
     *
     * @param string $type
     * @param array $data
     * @return array
     */
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
     * Given a rule type and a rule object which satisfies it, return
     * the actual assignment array of team and user
     *
     * @param string $type
     * @param array $rule
     * @return array
     */
    private function getDataForRuleAndType($type, $rule)
    {
        switch ($type) {
            case TaskAllocationRuleEntityService::TYPE_SIMPLE:
                return $this->buildDetails($rule['team']['id'], $rule['user']['id']);

            // no other allocation type is yet implemented as of OLCS-3406
            // no point putting the other types and a default as it'll be dead
            // code - the other private method is called first which already throws
            // an exception
        }
    }

    /**
     * Fall back on system configuration to populate user and team
     *
     * @return array
     */
    private function getDefaultAllocation()
    {
        $system = $this->getServiceLocator()->get('Entity\SystemParameter');

        return $this->buildDetails(
            $system->getValue('task.default_team'),
            $system->getValue('task.default_user')
        );
    }

    /**
     * Helper to build up an array out of a team and optional user. This
     * keeps the names of the keys in one place
     *
     * @param int $team
     * @param int $user
     *
     * @return array
     */
    private function buildDetails($team, $user = null)
    {
        return [
            'assignedToTeam' => $team,
            'assignedToUser' => $user
        ];
    }

    /**
     * Validate an array of input data
     *
     * @param array $data
     */
    private function validateData(array $data)
    {
        if (!isset($data['category'])) {
            throw new InvalidArgumentException('Input data is missing required "category" key');
        }
    }
}
