<?php
namespace Common\Service\Document\Bookmark;

/**
 * Licence - Review Date
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ReviewDate extends SingleValueAbstract
{
    const SERVICE = 'Licence';
    const FORMATTER = 'Date';
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'reviewDate';
}
