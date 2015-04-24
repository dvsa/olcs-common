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
class BrNPNo extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return isset($data['busRegId']) ? [
            'service' => 'PublicationLink',
            'data' => [
                'busReg' => $data['busRegId']
            ],
            'bundle' => [
                'children' => [
                    'publication',
                ],
            ],
        ] : null;
    }

    public function render()
    {
        if (empty($this->data['Results'])) {
            return '';
        }

        // get the last record
        $publicationLink = array_pop($this->data['Results']);

        return !empty($publicationLink['publication']['publicationNo']) ?
            $publicationLink['publication']['publicationNo'] : '';
    }
}
