<?php

/**
 * Transport Manager Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Helper;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\TransportManagerHelperService;
use Common\Service\Data\CategoryDataService;

/**
 * Transport Manager Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerHelperServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new TransportManagerHelperService();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetCertificateFiles()
    {
        $tmId = 111;

        $mockTm = m::mock();

        $this->sm->setService('Entity\TransportManager', $mockTm);

        $mockTm->shouldReceive('getDocuments')
            ->with(
                111,
                null,
                null,
                CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
                CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
            )
            ->andReturn('RESPONSE');

        $response = $this->sut->getCertificateFiles($tmId);

        $this->assertEquals('RESPONSE', $response);
    }

    public function testGetCertificateFileData()
    {
        $tmId = 111;
        $file = ['name' => 'foo.txt'];

        $expected = [
            'transportManager' => 111,
            'description' => 'foo.txt',
            'category' => CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
            'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
        ];

        $response = $this->sut->getCertificateFileData($tmId, $file);

        $this->assertEquals($expected, $response);
    }
}
