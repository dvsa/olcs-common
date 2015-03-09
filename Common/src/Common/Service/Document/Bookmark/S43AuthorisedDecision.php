<?php

namespace Common\Service\Document\Bookmark;

/**
 * S43AuthorisedDecision
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class S43AuthorisedDecision extends StatementFlatAbstract
{
    const FORMATTER = null;
    const FIELD  = 'authorisersDecision';
    const SERVICE = 'Statement';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'statement';
}
