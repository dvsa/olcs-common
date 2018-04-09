<?php

namespace Common\Service\Table\Formatter;

/**
 * Event History User
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class EventHistoryUser implements FormatterInterface
{
    /**
     * Format
     *
     * @param array $data   Event data
     * @param array $column Column data
     * @param null  $sm     Service Manager
     *
     * @return string
     */
    public static function format($data, $column = [], $sm = null)
    {
        $internalMarker = isset($data['user']['team'])
            ? ' ' . $sm->get('Translator')->translate('internal.marker')
            : '';

        if ($data['changeMadeBy'] !== null) {
            return $data['changeMadeBy'] . $internalMarker;
        }

        if (isset($data['user']['contactDetails']['person'])) {
            $person = $data['user']['contactDetails']['person'];
            if (!empty($person['forename']) && !empty($person['familyName'])) {
                return $person['forename'] . ' ' . $person['familyName'] . $internalMarker;
            }
        }

        return $data['user']['loginId'] . $internalMarker;
    }
}
