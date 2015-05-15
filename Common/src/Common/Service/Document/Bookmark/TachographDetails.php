<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Entity\LicenceEntityService;

/**
 * TachographDetails bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TachographDetails extends DynamicBookmark
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
                    'tachographIns'
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

        $checkboxes = $this->getCheckboxesBookmarks($this->data['tachographIns']['id']);
        $content = [
            'Address' => $this->data['tachographInsName'],
            'checkbox1' => $checkboxes['checkbox1'],
            'checkbox2' => $checkboxes['checkbox2'],
            'checkbox3' => $checkboxes['checkbox3']
        ];

        $snippet = $this->getSnippet('TachographDetails');
        $parser  = $this->getParser();

        return $parser->replace($snippet, $content);
    }

    protected function getCheckboxesBookmarks($tachographInsId)
    {
        return [
            'checkbox1' => ($tachographInsId == LicenceEntityService::LICENCE_TACH_INTERNAL) ? 'X' : '',
            'checkbox2' => ($tachographInsId == LicenceEntityService::LICENCE_TACH_EXTERNAL) ? 'X' : '',
            'checkbox3' => ($tachographInsId == LicenceEntityService::LICENCE_TACH_NA) ? 'X' : ''
        ];
    }
}
