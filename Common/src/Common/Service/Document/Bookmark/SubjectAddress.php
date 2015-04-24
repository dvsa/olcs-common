<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Subject address bookmark
 */
class SubjectAddress extends DynamicBookmark
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
                    'opposer' => [
                        'children' => [
                            'contactDetails' => [
                                'children' => [
                                    'address'
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
        if (isset($this->data['opposer']['contactDetails']['address'])) {
            return Formatter\Address::format($this->data['opposer']['contactDetails']['address']);
        }
        return '';
    }
}
