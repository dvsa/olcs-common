<?php

namespace Common\Service\Data;

use Common\Service\Cqrs\Response;
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
    /**
     * Request Address by Uprn from PostCode Api
     *
     * @param string $uprn Uprn
     *
     * @return mixed
     * @throws UnexpectedResponseException
     */
    public function getAddressForUprn($uprn)
    {
        $dtoData = GetAddress::create(['uprn' => $uprn]);

        /** @var Response $response */
        $response = $this->handleQuery($dtoData);
        if ($response->isServerError() || $response->isClientError() || !$response->isOk() ||
            !count($response->getResult()['results'])) {
            throw new UnexpectedResponseException('unknown-error');
        }
        return $response->getResult()['results'][0];
    }

    /**
     * Request Addresses by Post from PostCode Api
     *
     * @param string $postcode Post Code
     *
     * @return mixed
     * @throws UnexpectedResponseException
     */
    public function getAddressesForPostcode($postcode)
    {
        $dtoData = GetList::create(['postcode' => $postcode]);

        /** @var Response $response */
        $response = $this->handleQuery($dtoData);

        if ($response->isServerError() || $response->isClientError() || !$response->isOk()) {
            throw new UnexpectedResponseException('unknown-error');
        }
        return $response->getResult()['results'];
    }
}
