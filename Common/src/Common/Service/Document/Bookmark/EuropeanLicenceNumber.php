<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * European Licence Number bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class EuropeanLicenceNumber extends DynamicBookmark
{
    const ISSUE_NO_PAD_LENGTH = 5;

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'CommunityLic',
            'data' => [
                'id' => $data['communityLic']
            ],
            'bundle' => [
                'children' => [
                    'licence'
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        $issueNo = str_pad($this->data['issueNo'], self::ISSUE_NO_PAD_LENGTH, '0', STR_PAD_LEFT);

        return $this->data['licence']['licNo'] . '/' . $issueNo;
    }
}
