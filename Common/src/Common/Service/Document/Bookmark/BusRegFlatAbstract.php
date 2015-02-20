<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Document\Bookmark\Formatter;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
abstract class BusRegFlatAbstract extends DynamicBookmark
{

    const CLASS_NAMESPACE = __NAMESPACE__;
    const FORMATTER = null;
    const BR_FIELD = null;

    public function getQuery(array $data)
    {
        return [
            'service' => 'BusReg',
            'data' => [
                'id' => $data['busRegId']
            ],
            'bundle' => [],
        ];
    }

    public function render()
    {
        $value = $this->data[static::BR_FIELD];

        $formatter = static::FORMATTER;

        if (!is_null($formatter)) {

            /**
             * @var \Common\Service\Document\Bookmark\Formatter\Date $class
             */
            $class = __NAMESPACE__ . '\Formatter\\' . $formatter;

            $value = $class::format((array)$value);
        }

        return $value;
    }
}
