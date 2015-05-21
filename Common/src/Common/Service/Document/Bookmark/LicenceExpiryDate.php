<?php

namespace Common\Service\Document\Bookmark;

/**
 * Licence Expiry Date bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceExpiryDate extends SingleValueAbstract
{
    const SERVICE = 'Licence';
    const FORMATTER = 'Date';
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'expiryDate';
}
