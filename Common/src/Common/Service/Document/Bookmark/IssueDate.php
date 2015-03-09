<?php

namespace Common\Service\Document\Bookmark;

/**
 * Licence - In Force Date
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class IssueDate extends SingleValueAbstract
{
    const SERVICE = 'Licence';
    const FORMATTER = 'Date';
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'inForceDate';
}
