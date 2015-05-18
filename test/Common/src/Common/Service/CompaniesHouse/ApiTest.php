<?php

/**
 * Companies House Api Service test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\CompaniesHouse;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\CompaniesHouse\Api as Sut;
use CommonTest\Bootstrap;

/**
 * Companies House Api Service test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApiTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        parent::setUp();

        $this->sut = new Sut();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * Test get company profile
     */
    public function testGetCompanyProfile()
    {
        // data
        $companyNumber = '01234567';

        // mocks
        $restHelperMock = m::mock();
        $this->sm->setService('Helper\Rest', $restHelperMock);

        // expectations
        $restHelperMock
            ->shouldReceive('sendGet')
            ->with(
                'companies_house_rest\\company',
                [$companyNumber],
                true
            )
            ->once()
            ->andReturn(['DATA']);

        // assertions
        $this->assertEquals(['DATA'], $this->sut->getCompanyProfile($companyNumber));
    }
}
