<?php

/**
 * UnlinkedTm.php
 */

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Class UnlinkedTm
 *
 * Returns all the transport managers for and family names.
 *
 * @package Common\Service\Document\Bookmark
 *
 * @author Josh Curtis <josh.curtis@valtech.com>
 */
class UnlinkedTm extends DynamicBookmark
{
    /**
     * Get the query, this query returns the licences transport managers contact
     * details.
     *
     * @param array $data The licence data
     *
     * @return array
     */
    public function getQuery(array $data)
    {
        $query = array(
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
                                    'homeCd'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $query;
    }

    /**
     * Return the TM's fore and surnames.
     *
     * @return string The TM fore and family names.
     */
    public function render()
    {
        $licences = $this->data['tmLicences'];

        if(count($licences) === 0) {
            return "To be nominated.";
        }

        $output = '';
        foreach ($licences as $licence) {
            $person = $licence['transportManager']['homeCd'];
            $output .= $person['forename'] . ' ' . $person['familyName'] . '\n';
        }

        return $output;
    }
}
