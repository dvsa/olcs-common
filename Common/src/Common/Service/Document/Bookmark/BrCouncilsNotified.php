<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class BrCouncilsNotified extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return [
            'service' => 'BusReg',
            'data' => [
                'id' => $data['busRegId']
            ],
            'bundle' => [
                'properties' => [
                    'id'
                ],
                'children' => [
                    'localAuthoritys' => [
                        'bundle' => [
                            'properties' => [
                                'description'
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }

    public function render()
    {
        $localAuthoritys = implode(
            ', ',
            array_map(
                function ($item) {
                    return $item['description'];
                },
                $this->data['localAuthoritys']
            )
        );

        return $localAuthoritys;
    }
}
