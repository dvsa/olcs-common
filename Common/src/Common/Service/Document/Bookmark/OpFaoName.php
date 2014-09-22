<?php
namespace Common\Service\Document\Bookmark;

class OpFaoName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'properties' => ['organisation'],
                'children' => [
                    'organisation' => [
                        'properties' => ['contactDetails'],
                        'children' => [
                            'contactDetails' => [
                                'properties' => ['contactType', 'fao'],
                                'children' => [
                                    'contactType' => [
                                        'properties' => ['id']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function format()
    {
        foreach ($this->data['organisation']['contactDetails'] as $contactDetail) {
            if ($contactDetail['contactType']['id'] === 'ct_corr') {
                return $contactDetail['fao'];
            }
        }
    }
}
