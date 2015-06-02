<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Registered Number bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class RegisteredNumber extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'organisation'
                ]
            ]
        ];
    }

    public function render()
    {
        if (isset($this->data['organisation']['companyOrLlpNo'])) {
            return $this->data['organisation']['companyOrLlpNo'];
        }
        return '';
    }
}
