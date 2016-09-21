<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * TransportManagerName Formatter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerName extends Name
{
    /**
     * Format
     *
     * @param array                   $data   Row Data
     * @param array                   $column Col params
     * @param ServiceLocatorInterface $sm     Service manager
     *
     * @return string
     */
    public static function format($data, array $column = [], ServiceLocatorInterface $sm = null)
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
                    $html = sprintf(
                        '<b><a href="%s">%s</a></b>',
                        static::getInternalUrl($data, $sm),
                        $name
                    );
                    break;
                case 'variation':
                    $html = sprintf(
                        '%s <b><a href="%s">%s</a></b> %s',
                        static::getActionName($data, $sm),
                        static::getInternalUrl($data, $sm),
                        $name,
                        static::getStatusHtml($data, $sm)
                    );
                    break;
                case 'application':
                    $html = sprintf(
                        '<b><a href="%s">%s</a></b> %s',
                        static::getInternalUrl($data, $sm),
                        $name,
                        static::getStatusHtml($data, $sm)
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
                    // only hyperlink if Added or Updated
                    if (isset($data['action']) && ($data['action'] == 'A' || $data['action'] == 'U')) {
                        $html = sprintf(
                            '%s <b><a href="%s">%s</a></b> %s',
                            static::getActionName($data, $sm),
                            static::getExternalUrl($data, $sm, $column['lva']),
                            $name,
                            static::getStatusHtml($data, $sm)
                        );
                    } else {
                        $html = sprintf(
                            '%s <b>%s</b> %s',
                            static::getActionName($data, $sm),
                            $name,
                            static::getStatusHtml($data, $sm)
                        );
                    }
                    break;
                case 'application':
                    $html = sprintf(
                        '<b><a href="%s">%s</a></b> %s',
                        static::getExternalUrl($data, $sm, $column['lva']),
                        $name,
                        static::getStatusHtml($data, $sm)
                    );
                    break;
            }
        }

        return $html;
    }

    /**
     * Get URL for the Transport Managers name
     *
     * @param array                   $data Row Data
     * @param ServiceLocatorInterface $sm   Service manager
     * @param string                  $lva  Type (Lic|Var|App)
     *
     * @return string
     */
    protected static function getExternalUrl($data, $sm, $lva)
    {
        $route = 'lva-' . $lva . '/transport_manager_details';

        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute($route, ['action' => null, 'child_id' => $data['id']], [], true);

        return $url;
    }

    /**
     * Get URL for the Transport Managers name
     *
     * @param array                   $data Row Data
     * @param ServiceLocatorInterface $sm   Service manager
     *
     * @return string
     */
    protected static function getInternalUrl($data, $sm)
    {
        $transportManagerId = $data['transportManager']['id'];
        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute(
            'transport-manager/details', ['transportManager' => $transportManagerId], [], true
        );

        return $url;
    }

    /**
     * Convert action eg "U" into its description
     *
     * @param string                  $data Row Data
     * @param ServiceLocatorInterface $sm   Service Manager
     *
     * @return string Description
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
     * @param array                   $data Row Data
     * @param ServiceLocatorInterface $sm   Service Manager
     *
     * @return string HTML
     */
    protected static function getStatusHtml($data, $sm)
    {
        $viewHelper = $sm->get('ViewHelperManager')->get('transportManagerApplicationStatus');

        $id = (isset($data['status']['id'])) ? $data['status']['id'] : '';
        $description = (isset($data['status']['description'])) ? $data['status']['description'] : '';

        return $viewHelper->render($id, $description);
    }
}
