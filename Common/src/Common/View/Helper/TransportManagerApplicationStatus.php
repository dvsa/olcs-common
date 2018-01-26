<?php

namespace Common\View\Helper;

use Common\Service\Entity\TransportManagerApplicationEntityService;
use Zend\View\Helper\AbstractHelper;

/**
 * Transport Manager Application Status view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerApplicationStatus extends AbstractHelper
{
    protected static $statusColors = [
        TransportManagerApplicationEntityService::STATUS_INCOMPLETE => 'red',
        TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE => 'orange',
        TransportManagerApplicationEntityService::STATUS_TM_SIGNED => 'orange',
        TransportManagerApplicationEntityService::STATUS_OPERATOR_SIGNED => 'green',
        TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION => 'green',
        TransportManagerApplicationEntityService::STATUS_RECEIVED => 'green',
    ];

    /**
     * Generate HTML to display a Transport Manager Application Status
     *
     * @param int    $statusId    Status Id
     * @param string $description Description
     *
     * @return string HTML
     */
    public function render($statusId, $description)
    {
        $statusClass = (isset(self::$statusColors[$statusId])) ? ' ' . self::$statusColors[$statusId] : '';

        if (!isset($description) || $description === '') {
            return '';
        }

        return sprintf(
            '<span class="status%s">%s</span>',
            $statusClass,
            $this->getView()->translate($description)
        );
    }

    /**
     * Generate HTML to display a Transport Manager Application Status
     *
     * @param int    $statusId    Status Id
     * @param string $description Description
     *
     * @return string HTML
     */
    public function __invoke($statusId, $description)
    {
        return $this->render($statusId, $description);
    }
}
