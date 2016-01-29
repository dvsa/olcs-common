<?php

/**
 * Variation Completion Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Variation Completion Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationCompletionEntityService extends ApplicationCompletionEntityService
{
    const STATUS_UNCHANGED = 0;
    const STATUS_REQUIRES_ATTENTION = 1;
    const STATUS_UPDATED = 2;

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'ApplicationCompletion';

    public function updateCompletionStatuses($applicationId, $statuses)
    {
        $data = parent::getCompletionStatuses($applicationId);

        $stringHelper = $this->getServiceLocator()->get('Helper\String');

        foreach ($statuses as $section => $value) {
            $sectionProperty = lcfirst($stringHelper->underscoreToCamel($section)) . 'Status';

            $data[$sectionProperty] = $value;
        }

        $this->save($data);
    }

    public function getCompletionStatuses($applicationId)
    {
        $data = parent::getCompletionStatuses($applicationId);

        $statuses = [];

        $stringHelper = $this->getServiceLocator()->get('Helper\String');

        foreach ($data as $key => $value) {
            if (preg_match('/^([a-zA-Z]+)Status$/', $key, $matches)) {
                $section = strtolower($stringHelper->camelToUnderscore($matches[1]));

                $statuses[$section] = (int)$value;
            }
        }

        return $statuses;
    }
}
