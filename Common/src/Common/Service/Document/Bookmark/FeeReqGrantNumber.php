<?php

namespace Common\Service\Document\Bookmark;

/**
 * Fee request grant number bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeeReqGrantNumber extends SingleValueAbstract
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Fee',
            'data' => [
                'id' => $data['fee']
            ],
            'bundle' => [
                'children' => ['licence']
            ]
        ];

        return $query;
    }

    public function render()
    {
        return $this->data['licence']['licNo'] . ' / ' . $this->data['id'];
    }
}
