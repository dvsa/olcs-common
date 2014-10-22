<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Vehicle row bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class VehicleRow extends DynamicBookmark
{
    /**
     * Let the parser know we've already formatted our content by the
     * time it has been rendered
     */
    const PREFORMATTED = true;

    private $vehicleBundle = [
        'properties' => [
            'licenceVehicles'
        ],
        'children' => [
            'licenceVehicles' => [
                'properties' => [
                    'specifiedDate'
                ],
                'children' => [
                    'vehicle' => [
                        'properties' => [
                            'vrm',
                            'platedWeight'
                        ]
                    ]
                ]
            ]
        ]
    ];

    public function getQuery(array $data)
    {
        return [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => $this->vehicleBundle
        ];
    }

    public function render()
    {
        if (!isset($this->data['licenceVehicles'])) {
            return '';
        }

        $vehicles = $this->data['licenceVehicles'];

        $snippet = $this->getSnippet();
        $parser  = $this->getParser();

        $str = '';
        foreach ($vehicles as $vehicle) {
            $tokens = [
                'SPEC_DATE'     => date('d-M-Y', strtotime($vehicle['specifiedDate'])),
                'PLATED_WEIGHT' => $vehicle['vehicle']['platedWeight'],
                'REG_MARK'      => $vehicle['vehicle']['vrm']
            ];
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }
}
