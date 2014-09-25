<?php

namespace Common\Service\Document\Bookmark\Base;

/**
 * Date Delta bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class DateDelta extends StaticBookmark
{
    const FORMAT = "d/m/Y";
    const DELTA  = "+0";

    public function render()
    {
        $timestamp = strtotime(static::DELTA . " days");
        return date(static::FORMAT, $timestamp);
    }
}
