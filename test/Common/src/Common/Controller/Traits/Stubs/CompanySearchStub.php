<?php declare(strict_types=1);


namespace CommonTest\Controller\Traits\Stubs;

use Common\Controller\Traits\CompanySearch;

class CompanySearchStub
{
    use CompanySearch;

    public $stubResponse;

    public function handleQuery($dto)
    {
        $this->stubResponse->dto = $dto;
        return $this->stubResponse;
    }
}
