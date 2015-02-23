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
class StatementContactType extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return [
            'service' => 'Statement',
            'data' => [
                'id' => $data['statement']
            ],
            'bundle' => [
                'properties' => [
                    'id'
                ],
                'children' => [
                    'contactType' => [
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
        return $this->data['contactType']['description'];
    }
}
