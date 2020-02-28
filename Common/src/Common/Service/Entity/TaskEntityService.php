<?php

/**
 * Task Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Task Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaskEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Task';

    public function closeByQuery(array $query = array())
    {
        $query['isClosed'] = 'N';

        $results = $this->getAll($query);

        if (empty($results['Results'])) {
            return;
        }

        $updates = array();

        foreach ($results['Results'] as $task) {
            $updates[] = array(
                'id' => $task['id'],
                'isClosed' => 'Y',
                '_OPTIONS_' => array('force' => true)
            );
        }

        $updates['_OPTIONS_']['multiple'] = true;

        $this->put($updates);
    }
}
