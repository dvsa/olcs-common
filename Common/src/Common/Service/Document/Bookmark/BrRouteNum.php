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
class BrRouteNum extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return isset($data['busRegId']) ? [
            'service' => 'BusReg',
            'data' => [
                'id' => $data['busRegId']
            ],
            'bundle' => [
                'children' => [
                    'otherServices',
                ],
            ],
        ] : null;
    }

    public function render()
    {
        if (empty($this->data)) {
            return '';
        }

        $value = $this->data['serviceNo'];

        $otherServices
            = !empty($this->data['otherServices']) ?
                implode(', ', array_column($this->data['otherServices'], 'serviceNo')) : null;

        if (!empty($otherServices)) {
            $value .= sprintf(" (%s)", $otherServices);
        }

        return $value;
    }
}
