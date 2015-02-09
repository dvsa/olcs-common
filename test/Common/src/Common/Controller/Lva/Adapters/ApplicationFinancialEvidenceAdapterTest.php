<?php

/**
 * Abstract Financial Evidence Adapter Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace OlcsTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationFinancialEvidenceAdapter;
use Common\Service\Entity\LicenceEntityService as Licence;
use Common\Service\Entity\ApplicationEntityService as Application;
use CommonTest\Bootstrap;

/**
 * Abstract Financial Evidence Adapter Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApplicationFinancialEvidenceAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationFinancialEvidenceAdapter();
        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);

        $applicationId = 123;

        $applicationData = [
            'id' => $applicationId,
            'totAuthVehicles' => 3,
            'status' => [ 'id' => Application::APPLICATION_STATUS_NOT_SUBMITTED ],
            'licence' => [
                'id' => 234,
                'organisation' => [ 'id' => 99 ],
            ],
            'licenceType' => [ 'id' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL ],
            'goodsOrPsv' => [ 'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE ],
        ];
        $mockApplicationEntityService = m::mock()
            ->shouldReceive('getDataForFinancialEvidence')
            ->once()
            ->with($applicationId)
            ->andReturn($applicationData)
            ->getMock();

        $this->sm->setService('Entity\Application', $mockApplicationEntityService);

        $licences = [
            [
                'id' => 235,
                'status' => [ 'id' => Licence::LICENCE_STATUS_VALID ],
                'goodsOrPsv' => [ 'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE ],
                'licenceType' => [ 'id' => Licence::LICENCE_TYPE_RESTRICTED ],
                'totAuthVehicles' => 3,

            ],
            [
                'id' => 236,
                'status' => [ 'id' => Licence::LICENCE_STATUS_GRANTED ], // should be ignored
                'goodsOrPsv' => [ 'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE ],
                'licenceType' => [ 'id' => Licence::LICENCE_TYPE_RESTRICTED ],
                'totAuthVehicles' => 69,

            ],
            [
                'id' => 237,
                'status' => [ 'id' => Licence::LICENCE_STATUS_SUSPENDED ], // should be included
                'goodsOrPsv' => [ 'id' => Licence::LICENCE_CATEGORY_PSV ],
                'licenceType' => [ 'id' => Licence::LICENCE_TYPE_RESTRICTED ],
                'totAuthVehicles' => 1,

            ],
        ];

        $mockOrganisationEntityService = m::mock()
            ->shouldReceive('getLicences')
            ->once()
            ->with(99)
            ->andReturn($licences)
            ->getMock();


        $anotherApplication  = [
            'id' => 124,
            'totAuthVehicles' => 0,
            'status' => [ 'id' => Application::APPLICATION_STATUS_UNDER_CONSIDERATION ],
            'licence' => [
                'id' => 237,
                'organisation' => [ 'id' => 99 ],
            ],
            'licenceType' => [ 'id' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL ],
            'goodsOrPsv' => [ 'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE ],
        ];

        $currentApplications = [
            $applicationData,
            $anotherApplication
        ];

        $mockOrganisationEntityService
            ->shouldReceive('getNewApplications')
            ->once()
            ->with(99)
            ->andReturn($currentApplications);

        $this->sm->setService('Entity\Organisation', $mockOrganisationEntityService);
    }

    public function testGetRequiredFinance()
    {
        // For an operator:
        //  * with a goods standard international application with 3 vehicles,
        //    the finance is £7000 + (2 x £3900) = £14,800
        //  * plus a goods restricted licence with 3 vehicles, the finance is (3 x £1700) = £5,100
        //  * plus a psv restricted licence with 1 vehicle, the finance is £2,700
        //  * The total required finance is £14,800 + £5,100 + £2,700 = £22,600
        $expected = 22600; // NOT 24000

        $this->assertEquals($expected, $this->sut->getRequiredFinance(123));
    }


    public function testGetTotalNumberOfAuthorisedVehicles()
    {
        $this->assertEquals(7, $this->sut->getTotalNumberOfAuthorisedVehicles(123));
    }
}
