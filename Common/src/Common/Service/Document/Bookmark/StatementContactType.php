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
        return isset($data['statement']) ? [
            'service' => 'Statement',
            'data' => [
                'id' => $data['statement']
            ],
            'bundle' => [
                'children' => [
                    'contactType',
                ],
            ],
        ] : null;
    }

    public function render()
    {
        return isset($this->data['contactType']['description']) ? $this->data['contactType']['description'] : '';
    }
}
