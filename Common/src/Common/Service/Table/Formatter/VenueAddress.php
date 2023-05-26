<?php

/**
 * Venue Address formatter
 */

namespace Common\Service\Table\Formatter;

/**
 * Venue Address formatter
 */
class VenueAddress implements FormatterPluginManagerInterface
{
    private Address $addressFormatter;

    /**
     * @param Address $addressFormatter
     */
    public function __construct(Address $addressFormatter)
    {
        $this->addressFormatter = $addressFormatter;
    }
    /**
     * Format a venue address
     *
     * @param  array $data
     * @return string
     */
    public function format($data, $column = [])
    {
        if (!empty($data['venue'])) {
            // name and address
            return $data['venue']['name'] . ' - ' . $this->addressFormatter->format($data['venue']['address'], ['addressFields' => 'FULL']);
        } elseif (!empty($data['venueOther'])) {
            // other venue
            return $data['venueOther'];
        }

        return '';
    }
}
