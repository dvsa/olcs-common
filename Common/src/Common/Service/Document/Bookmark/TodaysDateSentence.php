<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DateDelta;

/**
 * Today's date in words bookmark
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class TodayDateSentence extends DateDelta
{
    const FORMAT = "j F Y";
}
