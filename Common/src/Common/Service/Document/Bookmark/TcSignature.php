<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Traffic Comissioner signature
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TcSignature extends DynamicBookmark
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
        return $this->getImage('TC_SIG_NORTHWESTERN', 610, 90);
    }
}
