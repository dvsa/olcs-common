<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * PiHearingVenue
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class PiHearingVenue extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return isset($data['hearing']) ? [
            'service' => 'PiHearing',
            'data' => [
                'id' => $data['hearing']
            ],
            'bundle' => [
                'children' => [
                    'piVenue',
                ],
            ],
        ] :  null;
    }

    public function render()
    {
        if (isset($this->data['piVenue']) && count($this->data['piVenue']) > 0) {
            return $this->data['piVenue']['name'];
        }

        return $this->data['venueOther'];
    }
}
