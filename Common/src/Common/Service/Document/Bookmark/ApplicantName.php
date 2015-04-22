<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Applicant name bookmark
 */
class ApplicantName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return isset($data['opposition']) ? [
            'service' => 'Opposition',
            'data' => [
                'id' => $data['opposition']
            ],
            'bundle' => [
                'children' => [
                    'licence' => [
                        'children' => [
                            'organisation' => [
                                'children' => [
                                    'tradingNames'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ] : null;
    }

    public function render()
    {
        if (isset($this->data['licence']['organisation'])) {
            return Formatter\OrganisationName::format($this->data['licence']['organisation']);
        }
        return '';
    }
}
