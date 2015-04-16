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

    public function testAlterResponsibilitiesFieldset()
    {
        // Params
        $fieldset = m::mock();
        $ocOptions = [
            111 => ['foo'],
            222 => ['bar']
        ];
        $otherLicencesTable = m::mock();

        // Mocks
        $mockFormHelper = m::mock();
        $mockTmTypeField = m::mock();
        $mockOtherLicenceField = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $fieldset->shouldReceive('get')
            ->once()
            ->with('operatingCentres')
            ->andReturn(
                m::mock()
                ->shouldReceive('setValueOptions')
                ->once()
                ->with($ocOptions)
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('tmType')
            ->andReturn($mockTmTypeField)
            ->shouldReceive('get')
            ->with('otherLicences')
            ->andReturn($mockOtherLicenceField);

        $mockFormHelper->shouldReceive('removeOption')
            ->once()
            ->with($mockTmTypeField, 'tm_t_B')
            ->shouldReceive('populateFormTable')
            ->with($mockOtherLicenceField, $otherLicencesTable);

        $this->sut->alterResponsibilitiesFieldset($fieldset, $ocOptions, $otherLicencesTable);
    }

    public function testGetResponsibilityFileData()
    {
        $tmId = 111;

        $expected = [
            'transportManager' => 111,
            'issuedDate' => '2014-01-20 10:10:10',
            'description' => 'Additional information',
            'category' => CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
            'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL
        ];

        // Mocks
        $mockDateHelper = m::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        // Expectations
        $mockDateHelper->shouldReceive('getDate')
            ->with(\DateTime::W3C)
            ->andReturn('2014-01-20 10:10:10');

        // Assertions
        $data = $this->sut->getResponsibilityFileData($tmId);

        $this->assertEquals($expected, $data);
    }

    public function testGetResponsibilityFiles()
    {
        $tmId = 111;
        $tmaId = 222;
        $stubbedTmaData = [
            'application' => [
                'id' => 333
            ]
        ];

        // Mocks
        $mockTma = m::mock();
        $mockTm = m::mock();
        $this->sm->setService('Entity\TransportManagerApplication', $mockTma);
        $this->sm->setService('Entity\TransportManager', $mockTm);

        // Expectations
        $mockTma->shouldReceive('getTransportManagerApplication')
            ->with($tmaId)
            ->andReturn($stubbedTmaData);

        $mockTm->shouldReceive('getDocuments')
            ->with(
                111,
                333,
                'application',
                CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
                CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL
            )
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getResponsibilityFiles($tmId, $tmaId));
    }
}
