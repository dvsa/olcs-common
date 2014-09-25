<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DateDelta;

/**
 * Letter date + 14 days class
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LetterDateAdd14Days extends DateDelta
{
    const DELTA = "+14";
}
