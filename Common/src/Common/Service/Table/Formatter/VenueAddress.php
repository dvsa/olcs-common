<?php

/**
 * Venue Address formatter
 */

namespace Common\Service\Table\Formatter;

/**
 * Venue Address formatter
 */
class VenueAddress implements FormatterInterface
{
    /**
     * Format a date
     *
     * @param array $data
     * @return string
     */
    public static function format($data)
    {
        if (!empty($data['venue'])) {
            // name and address
            return $data['venue']['name'].' - '.Address::format($data['venue']['address'], ['addressFields' => 'FULL']);

        } elseif (!empty($data['venueOther'])) {
            // other venue
            return $data['venueOther'];
        }

        return '';
    }
}
