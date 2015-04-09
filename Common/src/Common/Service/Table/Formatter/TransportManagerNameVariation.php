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
class TransportManagerNameVariation extends TransportManagerName implements FormatterInterface
{
    public static function format($data, $column = array(), $sm = null)
    {
        $name = parent::format($data, $column, $sm);

        if (isset($data['action'])) {
            // if deleted then remove the anchor tag
            if ($data['action'] === 'D' || $data['action'] === 'C') {
                $name = strip_tags($name, '<b><span>');
            }
            $name = static::getActionName($data['action'], $sm) .' '. $name;
        }

        return $name;
    }

    /**
     * Convert action eg "U" into its description
     *
     * @param string  $action 'U', 'A', etc
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string Description
     * @throws \InvalidArgumentException
     */
    protected static function getActionName($action, $sm)
    {
        $statusMaps = [
            'U' => 'tm_application.table.status.updated',
            'D' => 'tm_application.table.status.removed',
            'A' => 'tm_application.table.status.new',
            'C' => 'tm_application.table.status.current',
            'E' => '',
        ];

        if (!isset($statusMaps[$action])) {
            throw new \InvalidArgumentException("Action '{$action}' is unexpected.");
        }

        $translator = $sm->get('Helper\Translation');

        return $translator->translate($statusMaps[$action]);
    }
}
