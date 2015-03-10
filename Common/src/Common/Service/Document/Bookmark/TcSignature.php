<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\ImageBookmark;

/**
 * Traffic Comissioner signature
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TcSignature extends ImageBookmark
{
    const PREFORMATTED = true;

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'trafficArea'
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        // proof-of-concept for OLCS-7669 spike only - the image path
        // wouldn't be hardcoded like this!
        return $this->getImage('TC_SIG_NORTHWESTERN', 610, 90);
    }
}
