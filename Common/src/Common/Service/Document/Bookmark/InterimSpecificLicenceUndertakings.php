<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Licence - Interim Undertakings
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimSpecificLicenceUndertakings extends AbstractInterimConditionsUndertakings
{
    const CONDITION_TYPE = ConditionUndertakingEntityService::TYPE_UNDERTAKING;
}
