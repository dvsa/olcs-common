<?php declare(strict_types=1);


namespace CommonTest\Controller\Traits\Stubs;

use Common\Controller\Traits\CompanySearch;
use Common\Service\Cqrs\Exception\NotFoundException;

class CompanySearchStub
{
    use CompanySearch;

    public $stubResponse;

    public function handleQuery($dto)
    {
        $this->stubResponse->dto = $dto;
        return $this->stubResponse;
        //throw new NotFoundException('test');
    }

    public function renderForm($form)
    {
        $this->form = $form;
        return $form;
    }
}
