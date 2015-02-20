<?php

namespace Common\Service\Document\Bookmark;

/**
 * S9AuthorisedDecision
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class S9AuthorisedDecision extends StatementFlatAbstract
{
    const FORMATTER = null;
    const FIELD  = 'authorisersDecision';
    const SERVICE = 'Statement';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'statement';
}
