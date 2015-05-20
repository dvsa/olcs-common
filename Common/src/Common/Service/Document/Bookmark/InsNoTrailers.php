<?php

namespace Common\Service\Document\Bookmark;

/**
 * InsNoTrailers bookmark - number of weeks between trailer inspections
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InsNoTrailers extends SingleValueAbstract
{
    const SERVICE = 'Licence';
    const FORMATTER = null;
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'safetyInsTrailers';
}
