<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Entity\LicenceEntityService;

/**
 * Standard Conditions bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class StandardConditions extends DynamicBookmark
{
    /**
     * Let the parser know we've already formatted our content by the
     * time it has been rendered
     */
    const PREFORMATTED = true;

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'licenceType'
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

        $prefix = $this->data['niFlag'] === 'N' ? 'GB' : 'NI';
        $type = '';

        switch ($this->data['licenceType']['id']) {
            case LicenceEntityService::LICENCE_TYPE_RESTRICTED:
                $type = 'RESTRICTED';
                break;

            case LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL:
                $type = 'STANDARD';
                break;

            case LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL:
                $type = 'STANDARD_INT';
                break;
        }

        $path = $prefix . '_' . $type . '_' . 'LICENCE_CONDITIONS';

        $snippet = $this->getSnippet($path);

        return $this->getParser()->replace(
            $this->getSnippet($path),
            []
        );
    }
}
