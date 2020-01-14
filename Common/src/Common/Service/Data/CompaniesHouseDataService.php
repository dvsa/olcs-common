<?php

namespace Common\Service\Data;

use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use Dvsa\Olcs\Api\Domain\QueryHandler\CompaniesHouse\ByNumber;
use Dvsa\Olcs\Transfer\Query\CompaniesHouse\GetList;

/**
 * Companies House data service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CompaniesHouseDataService extends AbstractDataService
{
    /**
     * Search
     *
     * @param string $type  Type
     * @param string $value Value
     *
     * @return array
     * @throw UnexpectedResponseException
     */
    public function search($type, $value)
    {
        //$dtoData = GetList::create(['type' => $type, 'value' => $value]);
        $dtoData = \Dvsa\Olcs\Transfer\Query\CompaniesHouse\ByNumber::create(['companyNumber'=>$value]);
        $response = $this->handleQuery($dtoData);

        if (!$response->isOk()) {
            throw new UnexpectedResponseException('unknown-error');
        }

        return $this->formatResult($response->getResult());
    }
}
