<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Licence - Interim Conditions
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimSpecificLicenceConditions extends AbstractInterimConditionsUndertakings
{
    const CONDITION_TYPE = ConditionUndertakingEntityService::TYPE_CONDITION;
}
