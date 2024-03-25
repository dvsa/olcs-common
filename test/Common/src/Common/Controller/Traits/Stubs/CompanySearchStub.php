<?php declare(strict_types=1);

namespace CommonTest\Common\Controller\Traits\Stubs;

use Common\Controller\Traits\CompanySearch;

class CompanySearchStub
{
    public $form;
    use CompanySearch;

    public $stubResponse;

    public function handleQuery($dto)
    {
        $this->stubResponse->dto = $dto;
        return $this->stubResponse;
    }

    public function renderForm($form)
    {
        $this->form = $form;
        return $form;
    }
}
