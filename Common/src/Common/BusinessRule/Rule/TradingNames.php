<?php

/**
 * Trading Names
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;

/**
 * Trading Names
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TradingNames implements BusinessRuleInterface
{
    public function filter(array $tradingNames)
    {
        $filtered = [];

        foreach ($tradingNames as $tradingName) {
            $tradingName = trim($tradingName);

            if (!empty($tradingName)) {
                $filtered[] = [
                    'name' => $tradingName
                ];
            }
        }

        return $filtered;
    }

    public function validate(array $tradingNames, $orgId, $licenceId)
    {
        return [
            'organisation' => $orgId,
            'licence' => $licenceId,
            'tradingNames' => $tradingNames
        ];
    }
}
