<?php


namespace Common\Service\Data;

use Dvsa\Olcs\Transfer\Query\Surrender\ByLicence;

class Surrender extends AbstractDataService
{
    public function fetchSurrenderData(int $licenceId): array
    {
        $surrenderQuery = ByLicence::create(
            ['id' => $licenceId]
        );
    }
}
