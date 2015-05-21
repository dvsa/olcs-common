<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\VehicleEntityService;

/**
 * VehiclesSpecified bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VehiclesSpecified extends DynamicBookmark
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
                    'licenceVehicles' => [
                        'children' => [
                            'vehicle' => [
                                'children' => [
                                    'psvType'
                                ]
                            ]
                        ]
                    ],
                    'goodsOrPsv'
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
        $psvType = [
            VehicleEntityService::PSV_TYPE_SMALL => 'Max 8 seats',
            VehicleEntityService::PSV_TYPE_MEDIUM => '9 to 16 seats',
            VehicleEntityService::PSV_TYPE_LARGE => 'Over 16 seats'
        ];

        $isGoods = $this->data['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE;

        $header[] = [
            'BOOKMARK1' => 'Registration mark',
            'BOOKMARK2' => $isGoods ? 'Plated weight' : 'Vehicle type',
            'BOOKMARK3' => 'To continue to be specified on licence (Y/N)'
        ];

        $rows = [];
        foreach ($this->data['licenceVehicles'] as $licenceVehicle) {
            $vehicle = $licenceVehicle['vehicle'];
            $rows[] = [
                'BOOKMARK1' => $vehicle['vrm'],
                'BOOKMARK2' => $isGoods ? $vehicle['platedWeight'] : $psvType[$vehicle['psvType']['id']],
                'BOOKMARK3' => ''
            ];
        }

        $sortedVehicles = $this->sortVehicles($rows);

        $rows = array_pad($sortedVehicles, 15, ['BOOKMARK1' => '', 'BOOKMARK2' => '', 'BOOKMARK3' => '']);

        $allRows = array_merge($header, $rows);
        $snippet = $this->getSnippet('CHECKLIST_3CELL_TABLE');
        $parser  = $this->getParser();

        $str = '';
        foreach ($allRows as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }

    protected function sortVehicles($rows)
    {
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
        return $rows;
    }
}
