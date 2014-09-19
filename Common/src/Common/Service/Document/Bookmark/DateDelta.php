<?php
namespace Common\Service\Document\Bookmark;

abstract class DateDelta extends StaticBookmark
{
    const FORMAT = "d/m/Y";
    const DELTA  = "+0";

    public function format()
    {
        $timestamp = strtotime(static::DELTA . " days");
        return date(static::FORMAT, $timestamp);
    }
}
