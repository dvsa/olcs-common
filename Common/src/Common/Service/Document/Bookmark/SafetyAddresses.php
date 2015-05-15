<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * SafetyAddresses bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SafetyAddresses extends DynamicBookmark
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
                    'workshops' => [
                        'children' => [
                            'contactDetails' => [
                                'children' => [
                                    'person',
                                    'address'
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

        $rows = [];
        $addressFormatter = new Formatter\Address();
        $addressFormatter->setSeparator(', ');
        foreach ($this->data['workshops'] as $workshop) {
            $person = $workshop['contactDetails']['person'];
            $address = $workshop['contactDetails']['address'];
            $rows[] = [
                'Address' => $person['forename'] . ' ' . $person['familyName'] . ', ' .
                    $addressFormatter->format($address, ', '),
                'checkbox1' => $workshop['isExternal'] !== 'Y' ? 'X' : '',
                'checkbox2' => $workshop['isExternal'] === 'Y' ? 'X' : ''
            ];
        }
        // need to reset static property to the original value
        $addressFormatter->setSeparator("\n");

        $sortedRows = $this->sortSafetyAddresses($rows);
        $snippet = $this->getSnippet('SafetyAddresses');
        $parser  = $this->getParser();

        $str = '';
        foreach ($sortedRows as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }

    protected function sortSafetyAddresses($rows)
    {
        usort(
            $rows,
            function ($a, $b) {
                if ($a['Address'] == $b['Address']) {
                    return 0;
                } elseif ($a['Address'] < $b['Address']) {
                    return -1;
                } else {
                    return 1;
                }
            }
        );
        return $rows;
    }
}
