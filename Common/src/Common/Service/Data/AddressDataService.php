<?php

/**
 * Address Data Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Data;

use Dvsa\Olcs\Transfer\Query\Address\GetAddress;
use Dvsa\Olcs\Transfer\Query\Address\GetList;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Address Data Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AddressDataService extends AbstractDataService
{
    public function getAddressForUprn($uprn)
    {
        $dtoData = GetAddress::create(['uprn' => $uprn]);

        $response = $this->handleQuery($dtoData);
        if ($response->isServerError() || $response->isClientError() || !$response->isOk() ||
            !count($response->getResult()['results'])) {
            throw new UnexpectedResponseException('unknown-error');
        }
        return $response->getResult()['results'][0];
    }

    public function getAddressesForPostcode($postcode)
    {
        $dtoData = GetList::create(['postcode' => $postcode]);

        $response = $this->handleQuery($dtoData);

        if ($response->isServerError() || $response->isClientError() || !$response->isOk()) {
            throw new UnexpectedResponseException('unknown-error');
        }
        return $response->getResult()['results'];
    }
}
