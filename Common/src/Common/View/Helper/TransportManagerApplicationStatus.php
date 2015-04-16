<?php

/**
 * Transport Manager Application Status view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Common\Service\Entity\TransportManagerApplicationEntityService;

/**
 * Transport Manager Application Status view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerApplicationStatus extends AbstractHelper
{
    protected $statusColors = [
        TransportManagerApplicationEntityService::STATUS_INCOMPLETE => 'red',
        TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE => 'orange',
        TransportManagerApplicationEntityService::STATUS_TM_SIGNED => 'orange',
        TransportManagerApplicationEntityService::STATUS_OPERATOR_SIGNED => 'green',
        TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION => 'green',
    ];

    /**
     * Generate HTML to display a Transport Manager Application Status
     *
     * @param int $statusId
     * @param string $description
     * @return string HTML
     */
    public function render($statusId, $description)
    {
        $statusClass = (isset($this->statusColors[$statusId])) ? ' '. $this->statusColors[$statusId] : '';

        return sprintf('<span class="status%s">%s</span>', $statusClass, $description);
    }

    /**
     * Generate HTML to display a Transport Manager Application Status
     *
     * @param int $statusId
     * @param string $description
     * @return string HTML
     */
    public function __invoke($statusId, $description)
    {
        return $this->render($statusId, $description);
    }
}
