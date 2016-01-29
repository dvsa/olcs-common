<?php

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetails implements MapperInterface
{
    public static function mapFromResult(array $data)
    {
        $tradingNames = [];
        foreach ($data['tradingNames'] as $tradingName) {
            $tradingNames[] = $tradingName['name'];
        }

        return array(
            'version' => $data['version'],
            'data' => array(
                'companyNumber' => array(
                    'company_number' => $data['companyOrLlpNo']
                ),
                'tradingNames' => array(
                    'trading_name' => $tradingNames
                ),
                'name' => $data['name'],
                'type' => $data['type']['id'],
                'natureOfBusiness' => $data['natureOfBusiness']
            ),
            'registeredAddress' => $data['contactDetails']['address'],
        );
    }
}
