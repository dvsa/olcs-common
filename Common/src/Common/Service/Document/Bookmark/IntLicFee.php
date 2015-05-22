<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Interim Licene Fee bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IntLicFee extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Fee',
            'data' => [
                'id' => $data['fee']
            ],
            'bundle' => []
        ];

        return $query;
    }

    public function render()
    {
        if (isset($this->data['amount'])) {
            return number_format($this->data['amount']);
        }
        return '';
    }
}
