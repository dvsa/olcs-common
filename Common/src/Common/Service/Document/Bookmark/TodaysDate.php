<?php
namespace Common\Service\Document\Bookmark;

class TodaysDate extends StaticBookmark
{
    public function format()
    {
        return date("d/m/Y");
    }
}
