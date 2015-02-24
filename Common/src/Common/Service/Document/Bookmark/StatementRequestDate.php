<?php
namespace Common\Service\Document\Bookmark;

/**
 * StatementRequestDate
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class StatementRequestDate extends StatementFlatAbstract
{
    const FORMATTER = 'Date';
    const FIELD  = 'requestedDate';
    const SERVICE = 'Statement';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'statement';
}
