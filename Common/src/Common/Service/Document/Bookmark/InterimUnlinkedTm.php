<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Entity\LicenceEntityService;

/**
 * Returns all the transport managers associated with
 * a given application
 *
 * @package Common\Service\Document\Bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimUnlinkedTm extends DynamicBookmark
{
    /**
     * Get the query data to fetch back the relevant TMs
     *
     * @param array $data
     *
     * @return array
     */
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Application',
            'data' => [
                'id' => $data['application']
            ],
            'bundle' => [
                'children' => [
                    'licenceType',
                    'transportManagers' => [
                        'criteria' => [
                            'action' => ['A', 'U']
                        ],
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

    /**
     * Return the listed TMs on the application
     *
     * @return string
     */
    public function render()
    {
        if ($this->data['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_RESTRICTED) {
            return 'N/A';
        }
        $transportManagers = $this->data['transportManagers'];

        if (count($transportManagers) === 0) {
            return 'None added as part of this application';
        }

        $output = [];
        foreach ($transportManagers as $tm) {
            $person = $tm['transportManager']['homeCd']['person'];
            $output[] = Formatter\Name::format($person);
        }

        return implode("\n", $output);
    }
}
