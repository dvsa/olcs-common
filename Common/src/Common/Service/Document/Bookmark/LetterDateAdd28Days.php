<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DateDelta;

/**
 * Letter date + 28 days class
 */
class LetterDateAdd28Days extends DateDelta
{
    const DELTA = "+28";
}
