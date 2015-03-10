<?php

namespace Common\Service\Document\Bookmark;

/**
 * Licence - Expiry Date
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ContinuationDate extends SingleValueAbstract
{
    const SERVICE = 'Licence';
    const FORMATTER = 'Date';
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'expiryDate';
}
