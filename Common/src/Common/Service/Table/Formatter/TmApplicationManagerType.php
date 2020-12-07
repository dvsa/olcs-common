<?php

/**
 * Tm Application Manager Type formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Tm Application Manager Type formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmApplicationManagerType implements FormatterInterface
{
    /**
     * Tm Application Manager Type formatter
     *
     * @param array $row
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($row, $column = [], $sm = null)
    {
        $routeParams = [
            'id' => $row['id'],
            'action' => 'edit-tm-application',
            'transportManager' => $sm->get('Application')
                ->getMvcEvent()
                ->getRouteMatch()
                ->getParam('transportManager')
        ];
        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute(null, $routeParams);
        $translate = $sm->get('translator');
        switch ($row['action']) {
            case 'A':
                $status = $translate->translate('tm_application.table.status.new');
                break;
            case 'U':
                $status = $translate->translate('tm_application.table.status.updated');
                break;
            case 'D':
                $status = $translate->translate('tm_application.table.status.removed');
                break;
            default:
                $status = '';
        }
        return $row['action'] === 'D' ? trim($row['tmType']['description']  . ' ' . $status) :
            '<a href="' . $url . '">' . trim($row['tmType']['description']  . ' ' . $status) . '</a>';
    }
}
