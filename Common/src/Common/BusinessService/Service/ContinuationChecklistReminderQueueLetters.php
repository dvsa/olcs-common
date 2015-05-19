<?php

/**
 * Generate checklist reminder letters
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\BusinessService\Service;

use Common\BusinessService\BusinessServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\Response;

/**
 * Generate checklist reminder letters
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ContinuationChecklistReminderQueueLetters implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Generate checklist reminder letters
     *
     * @param array $params['continuationDetailIds'] Array of continuationDetailId
     *
     * @return ResponseInterface
     */
    public function process(array $params)
    {
        if (!isset($params['continuationDetailIds'])) {
            return new Response(Response::TYPE_FAILED, [], "'continuationDetailIds' parameter is missing");
        }

        $continuationDetailIds = $params['continuationDetailIds'];
        $queueMessages = [];
        foreach ($continuationDetailIds as $continuationDetailId) {
            $queueMessages[] = [
                'type' => \Common\Service\Entity\QueueEntityService::TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER,
                'entityId' => $continuationDetailId,
                'status' => \Common\Service\Entity\QueueEntityService::STATUS_QUEUED,
            ];
        }

        $this->getServiceLocator()->get('Entity\Queue')->multiCreate($queueMessages);
        return new Response(Response::TYPE_SUCCESS);
    }
}
