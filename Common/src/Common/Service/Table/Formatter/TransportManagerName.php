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
class TransportManagerName extends Name
{
    protected static $linkActions = [
        TransportManagerApplicationEntityService::STATUS_INCOMPLETE => 'details',
        TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE => 'awaiting-signature',
        TransportManagerApplicationEntityService::STATUS_TM_SIGNED => 'tm-signed',
        TransportManagerApplicationEntityService::STATUS_OPERATOR_SIGNED => 'operator-signed',
        TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION => 'postal-application',
    ];

    protected static $statusColors = [
        TransportManagerApplicationEntityService::STATUS_INCOMPLETE => 'red',
        TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE => 'orange',
        TransportManagerApplicationEntityService::STATUS_TM_SIGNED => 'orange',
        TransportManagerApplicationEntityService::STATUS_OPERATOR_SIGNED => 'green',
        TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION => 'green',
    ];

    public static function format($data, $column = array(), $sm = null)
    {
        $name = parent::format($data['name'], $column, $sm);

        if (!isset($column['internal']) || (!isset($column['lva']))) {
            return $name;
        }

        // default
        $html = $name;
        if ($column['internal']) {
            switch ($column['lva']) {
                case 'licence':
                    $html = $name;
                    break;
                case 'variation':
                    $html = sprintf(
                        '%s <b><a href="%s">%s</a></b> %s',
                        static::getActionName($data, $sm),
                        static::getInternalUrl($data, $sm),
                        $name,
                        static::getStatusHtml($data)
                    );
                    break;
                case 'application':
                    $html = sprintf(
                        '<b><a href="%s">%s</a></b> %s',
                        static::getInternalUrl($data, $sm),
                        $name,
                        static::getStatusHtml($data)
                    );
                    break;
            }
        } else {
            // External
            switch ($column['lva']) {
                case 'licence':
                    $html = $name;
                    break;
                case 'variation':
                    $html = sprintf(
                        '%s <b><a href="%s">%s</a></b> %s',
                        static::getActionName($data, $sm),
                        static::getExternalUrl($data, $sm),
                        $name,
                        static::getStatusHtml($data)
                    );
                    break;
                case 'application':
                    $html = sprintf(
                        '<b><a href="%s">%s</a></b> %s',
                        static::getExternalUrl($data, $sm),
                        $name,
                        static::getStatusHtml($data)
                    );
                    break;
            }
        }

        return $html;
    }

    /**
     * Get URL for the Transport managers name
     *
     * @param array $data
     *
     * @return string
     */
    protected static function getExternalUrl($data, $sm)
    {
        $action = isset(static::$linkActions[$data['status']['id']]) ?
            static::$linkActions[$data['status']['id']] :
            'details';

        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute(null, ['action' => $action, 'child_id' => $data['id']], [], true);

        return $url;
    }

    /**
     * Get URL for the Transport managers name
     *
     * @param array $data
     *
     * @return string
     */
    protected static function getInternalUrl($data, $sm)
    {
        $transportManagerId = $data['transportManager']['id'];
        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute('transport-manager', ['transportManager' => $transportManagerId], [], true);

        return $url;
    }

    /**
     * Convert action eg "U" into its description
     *
     * @param string  $action 'U', 'A', etc
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string Description
     * @throws \InvalidArgumentException
     */
    protected static function getActionName($data, $sm)
    {
        $statusMaps = [
            'U' => 'tm_application.table.status.updated',
            'D' => 'tm_application.table.status.removed',
            'A' => 'tm_application.table.status.new',
            'C' => 'tm_application.table.status.current',
        ];

        if (!isset($data['action']) || !isset($statusMaps[$data['action']])) {
            return '';
        }

        $translator = $sm->get('Helper\Translation');

        return $translator->translate($statusMaps[$data['action']]);
    }

    /**
     * Get the html for the status
     *
     * @param array $data
     * @return string HTML
     */
    protected static function getStatusHtml($data)
    {
        $statusClass = (isset($data['status']['id']) && isset(static::$statusColors[$data['status']['id']])) ?
            static::$statusColors[$data['status']['id']] :
            '';

        return sprintf('<span class="status %s">%s</span>', $statusClass, $data['status']['description']);
    }
}
