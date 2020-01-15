<?php declare(strict_types=1);

namespace CommonTest\Controller\Traits;

use Common\Controller\Traits\CompanySearch;
use Common\Form\Form;
use Common\Service\Helper\FormHelperService;
use CommonTest\Bootstrap;
use CommonTest\Controller\Traits\Stubs\CompanySearchStub;
use Dvsa\Olcs\Transfer\Query\CompaniesHouse\ByNumber;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class CompanySearchTest extends MockeryTestCase
{
    /**
     * @var CompanySearch
     */
    protected $sut;

    protected $sm;

    /** @var  m\MockInterface */
    protected $mockResp;

    public function setUp()
    {
        $this->sut = new CompanySearchStub();


        $this->sm = Bootstrap::getServiceManager();
        $this->mockResp = m::mock(\Zend\Http\Response::class);

        $this->sut->stubResponse = $this->mockResp;

    }

    public function testCompanySearch()
    {
        $mockHelperService = m::mock(FormHelperService::class);
        $data = [];
        $mockHelperService->shouldReceive('processCompanyNumberLookupForm')->andReturn($data);
        $form = new Form();

        $data = [
            'detailsFieldset' => 'detailsFieldset',
            'addressFieldset' => 'addressFieldset',
            'companyNumber' => 1
        ];
        $this->mockResp->shouldReceive('isOk')->andReturn(true);
        $this->mockResp->shouldReceive('getResult')->andReturn($data);
        $actual = $this->sut->populateCompanyDetails($mockHelperService, $form, $data['detailsFieldset'],
            $data['addressFieldset'], $data['companyNumber']);
        $dto = $this->sut->stubResponse->dto;
        $this->assertInstanceOf(ByNumber::class, $dto);
        var_dump($actual);

    }
}
