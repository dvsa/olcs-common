<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * TransportManagers bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagers extends DynamicBookmark
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
                    'tmLicences' => [
                        'children' => [
                            'transportManager' => [
                                'children' => [
                                    'homeCd' => [
                                        'children' => [
                                            'person'
                                        ]
                                    ]
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

        $header[] = [
            'BOOKMARK1' => 'List of transport managers (only applicable to standard licences)',
            'BOOKMARK2' => 'Date of birth (please complete if not shown)'
        ];
        $rows = [];
        foreach ($this->data['tmLicences'] as $tmLicence) {
            $person = $tmLicence['transportManager']['homeCd']['person'];
            $birthDate = new \DateTime($person['birthDate']);
            $rows[] = [
                'BOOKMARK1' => $person['forename'] . ' ' . $person['familyName'],
                'BOOKMARK2' => $birthDate->format('d/m/Y')
            ];
        }
        usort(
            $rows,
            function ($a, $b) {
                if ($a['BOOKMARK1'] == $b['BOOKMARK1']) {
                    return 0;
                } elseif ($a['BOOKMARK1'] < $b['BOOKMARK1']) {
                    return -1;
                } else {
                    return 1;
                }
            }
        );
        $rows = array_pad($rows, 5, ['BOOKMARK1' => '', 'BOOKMARK2' => '']);
        $allRows = array_merge($header, $rows);

        $snippet = $this->getSnippet('CHECKLIST_2CELL_TABLE');
        $parser  = $this->getParser();

        $str = '';
        foreach ($allRows as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }
}
