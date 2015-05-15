<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * PhoneNumbers bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PhoneNumbers extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'correspondenceCd' => [
                        'children' => [
                            'phoneContacts' => [
                                'children' => [
                                    'phoneContactType'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        if (empty($this->data)) {
            return '';
        }

        $header = [
            [
                'BOOKMARK1' => 'Phone number(s)',
                'BOOKMARK2' => ''
            ],
            [
                'BOOKMARK1' => 'Type of contact number',
                'BOOKMARK2' => 'Number'
            ]
        ];
        $numbers = [];
        foreach ($this->data['organisation']['correspondenceCd']['phoneContacts'] as $phoneContact) {
            $numbers[] = [
                'BOOKMARK1' => $phoneContact['phoneContactType']['description'],
                'BOOKMARK2' => $phoneContact['phoneNumber'],
                'type' => $phoneContact['phoneContactType']['id']
            ];
        }
        usort(
            $numbers,
            function ($a, $b) {
                $types = [
                    'phone_t_tel' => 1,
                    'phone_t_home' => 2,
                    'phone_t_mobile' => 3,
                    'phone_t_fax' => 4
                ];
                if ($types[$a['type']] == $types[$b['type']]) {
                    return 0;
                } elseif ($types[$a['type']] < $types[$b['type']]) {
                    return -1;
                } else {
                    return 1;
                }
            }
        );
        for ($i = 0; $i < count($numbers); $i++) {
            unset($numbers[$i]['type']);
        }
        $numbers = array_pad($numbers, 5, ['BOOKMARK1' => '', 'BOOKMARK2' => '']);
        $rows = array_merge($header, $numbers);

        $snippet = $this->getSnippet('CHECKLIST_2CELL_TABLE');
        $parser  = $this->getParser();

        $str = '';
        foreach ($rows as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }
}
