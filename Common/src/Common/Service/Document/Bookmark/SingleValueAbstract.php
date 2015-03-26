<?php
/**
 * SingleValueAbstract
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Document\Bookmark\Formatter;

/**
 * SingleValueAbstract
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
abstract class SingleValueAbstract extends DynamicBookmark
{
    const CLASS_NAMESPACE = __NAMESPACE__; // do not change/override this.
    const FORMATTER = null; // defaults to null
    const FIELD = null; // example
    const SERVICE = 'BusReg'; // example
    const SRCH_FLD_KEY = 'id'; // example
    const SRCH_VAL_KEY = 'busRegId'; // example
    const DEFAULT_VALUE = null;

    public function getQuery(array $data)
    {
        return [
            'service' => static::SERVICE,
            'data' => [
                static::SRCH_FLD_KEY => $data[static::SRCH_VAL_KEY]
            ],
            'bundle' => [],
        ];
    }

    public function render()
    {
        $value = $this->data[static::FIELD];

        $formatter = static::FORMATTER;

        if (!is_null($formatter)) {

            /**
             * @var \Common\Service\Document\Bookmark\Formatter\Date $class
             */
            $class = __NAMESPACE__ . '\Formatter\\' . $formatter;

            $value = $class::format((array)$value);
        }

        if (empty($value) && static::DEFAULT_VALUE !== null) {
            $value = static::DEFAULT_VALUE;
        }

        return $value;
    }
}
