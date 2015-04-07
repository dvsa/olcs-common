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
    protected static $linkActions = [
        TransportManagerApplicationEntityService::STATUS_INCOMPLETE => 'details',
        TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE => 'awaiting-signature',
        TransportManagerApplicationEntityService::STATUS_TM_SIGNED => 'tm-signed',
        TransportManagerApplicationEntityService::STATUS_OPERATOR_SIGNED => 'operator-signed',
        TransportManagerApplicationEntityService::STATUS_POSTAL_APPLCIATION => 'postal-application',
    ];

    protected static $statusColors = [
        TransportManagerApplicationEntityService::STATUS_INCOMPLETE => 'red',
        TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE => 'orange',
        TransportManagerApplicationEntityService::STATUS_TM_SIGNED => 'orange',
        TransportManagerApplicationEntityService::STATUS_OPERATOR_SIGNED => 'green',
        TransportManagerApplicationEntityService::STATUS_POSTAL_APPLCIATION => 'green',
    ];

    public static function format($data, $column = array(), $sm = null)
    {
        $name = parent::format($data, $column, $sm);
        $url = static::getUrl($data, $sm);

        return sprintf(
            '<b><a href="%s">%s</a></b> <span class="status %s">%s</span>',
            $url,
            $name,
            static::$statusColors[$data['status']['id']],
            $data['status']['description']
        );
    }

    /**
     * Get URL for the Transport managers name
     *
     * @param array $data
     *
     * @return string
     */
    protected static function getUrl($data, $sm)
    {
        $action = isset(static::$linkActions[$data['status']['id']]) ?
            static::$linkActions[$data['status']['id']] :
            'details';

        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute(null, ['action' => $action], [], true);

        return $url;
    }
}
