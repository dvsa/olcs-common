<?php

namespace Common\Service\Document\Bookmark;

/**
 * Licence Trailer Limit Bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceTrailerLimit extends SingleValueAbstract
{
    const SERVICE = 'Licence';
    const FORMATTER = null;
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'totAuthTrailers';
}
