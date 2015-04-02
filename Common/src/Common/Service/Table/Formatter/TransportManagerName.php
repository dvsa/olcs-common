<?php

/**
 * TransportManagerName Formatter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Service\Table\Formatter;

use Common\Service\Entity\TransportManagerApplicationEntityService;

/**
 * TransportManagerName Formatter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerName extends Name implements FormatterInterface
{
    public static function format($data, $column = array(), $sm = null)
    {
        $name = parent::format($data, $column, $sm);

        $actions = [
            TransportManagerApplicationEntityService::STATUS_INCOMPLETE => 'details',
            TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE => 'awaiting-signature',
            TransportManagerApplicationEntityService::STATUS_TM_SIGNED => 'tm-signed',
            TransportManagerApplicationEntityService::STATUS_OPERATOR_SIGNED => 'operator-signed',
            TransportManagerApplicationEntityService::STATUS_POSTAL_APPLCIATION => 'postal-application',
        ];

        $action = $actions[$data['status']['id']];

        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute(null, ['action' => $action], [], true);

        $statusColors = [
            TransportManagerApplicationEntityService::STATUS_INCOMPLETE => 'red',
            TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE => 'orange',
            TransportManagerApplicationEntityService::STATUS_TM_SIGNED => 'orange',
            TransportManagerApplicationEntityService::STATUS_OPERATOR_SIGNED => 'green',
            TransportManagerApplicationEntityService::STATUS_POSTAL_APPLCIATION => 'green',
        ];

        return sprintf(
            '<b><a href="%s">%s</a></b> <span class="status %s">%s</span>',
            $url,
            $name,
            $statusColors[$data['status']['id']],
            $data['status']['description']
        );
    }
}
