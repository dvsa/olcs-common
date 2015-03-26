<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Entity\ApplicationEntityService;

/**
 * Community Licence - Valid To
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateTo extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            [
                'service' => 'CommunityLic',
                'data' => [
                    'id' => $data['communityLic']
                ],
                'bundle' => [
                    'children' => [
                        'licence'
                    ]
                ]
            ],
            [
                'service' => 'Application',
                'data' => [
                    'id' => $data['application']
                ],
                'bundle' => [
                    'children' => [
                        'interimStatus'
                    ]
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        if (isset($this->data[1]['interimStatus']['id']) &&
            $this->data[1]['interimStatus']['id'] == ApplicationEntityService::INTERIM_STATUS_INFORCE) {
            $dateFrom = date('d/m/Y', strtotime($this->data[1]['interimEnd']));
        } else {
            $dateFrom = date("d/m/Y", strtotime($this->data[0]['licence']['expiryDate']));
        }
        return $dateFrom;
    }
}
