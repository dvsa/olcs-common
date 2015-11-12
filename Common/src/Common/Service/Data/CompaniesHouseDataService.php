<?php

/**
 * Companies House data service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Data;

use Dvsa\Olcs\Transfer\Query\CompaniesHouse\GetList;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Companies House data service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CompaniesHouseDataService extends AbstractDataService
{
    public function search($type, $value)
    {
        $dtoData = GetList::create(['type' => $type, 'value' => $value]);

        $response = $this->handleQuery($dtoData);
        if ($response->isServerError() || $response->isClientError() || !$response->isOk()) {
            throw new UnexpectedResponseException('unknown-error');
        }
        return $this->formatResult($response->getResult());
    }
}
