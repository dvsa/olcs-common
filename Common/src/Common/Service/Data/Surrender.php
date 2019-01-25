<?php


namespace Common\Service\Data;

use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use Dvsa\Olcs\Transfer\Query\Surrender\ByLicence;

class Surrender extends AbstractDataService
{
    public function fetchSurrenderData(int $licenceId): array
    {
        $surrenderQuery = ByLicence::create(
            ['id' => $licenceId]
        );

        $response = $this->handleQuery($surrenderQuery);
        if ($response->isOk()) {
            return $response->getResult();
        }
        throw new UnexpectedResponseException('unknown-error');
    }
}
