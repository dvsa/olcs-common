<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\ImageBookmark;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class BrLogo extends ImageBookmark
{
    const CONTAINER_WIDTH = 105;
    const CONTAINER_HEIGHT = 100;

    const IMAGE_PREFIX = 'TC_LOGO_';

    public function getQuery(array $data)
    {
        return isset($data['busRegId']) ? [
            'service' => 'BusReg',
            'data' => [
                'id' => $data['busRegId']
            ],
            'bundle' => [
                'children' => [
                    'licence' => [
                        'children' => [
                            'trafficArea'
                        ]
                    ]
                ],
            ],
        ] : null;
    }

    public function render()
    {
        if (empty($this->data)) {
            return '';
        }

        $key = !empty($this->data['licence']['trafficArea']['isScotland']) ? 'SCOTTISH' : 'OTHER';

        return $this->getImage(
            static::IMAGE_PREFIX . $key,
            static::CONTAINER_WIDTH,
            static::CONTAINER_HEIGHT
        );
    }
}
