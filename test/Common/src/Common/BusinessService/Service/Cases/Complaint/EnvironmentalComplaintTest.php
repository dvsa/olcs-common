<?php

/**
 * Environmental Complaint Test
 */
namespace CommonTest\BusinessService\Service\Cases\Complaint;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Cases\Complaint\EnvironmentalComplaint;
use Common\BusinessService\Response;

/**
 * Environmental Complaint Test
 */
class EnvironmentalComplaintTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $brm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new EnvironmentalComplaint();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessRuleManager($this->brm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    /**
     * @dataProvider processDataProvider
     */
    public function testProcess($params, $expectedTaskData)
    {
        // Data
        $id = 1;
        $personId = 2;
        $contactDetailsId = 3;
        $addressId = 4;
        $complainantForename = 'John';
        $complainantFamilyName = 'Smith';
        $previousComplainantForename = 'Alan';
        $previousComplainantFamilyName = 'Jones';

        $existingData = [
            'complainantContactDetails' => [
                'id' => $contactDetailsId,
                'version' => 2,
                'person' => [
                    'forename' => $previousComplainantForename,
                    'familyName' => $previousComplainantFamilyName
                ],
                'address' => [
                    'id' => $addressId
                ]
            ]
        ];

        if (!empty($params['data']['id'])) {
            $contactDetailsData = [
                'id' => $contactDetailsId,
                'address' => $addressId,
                'version' => 2,
                'person' => $personId,
            ];
        } else {
            $contactDetailsData = [
                'person' => $personId,
                'address' => $addressId,
                'contactType' => 'ct_complainant'
            ];
        }

        $personData = [
            'forename' => $complainantForename,
            'familyName' => $complainantFamilyName,
        ];

        $params['data']['complainantForename'] = $complainantForename;
        $params['data']['complainantFamilyName'] = $complainantFamilyName;

        // Mocks
        $environmentalComplaintRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $this->brm->setService('EnvironmentalComplaint', $environmentalComplaintRule);

        $mockTaskService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Cases\Complaint\EnvironmentalComplaintTask', $mockTaskService);

        $mockRestHelper = m::mock('RestHelper');

        $mockDataServiceManager = m::mock();

        $mockAddressEntity = m::mock('Common\Service\Data\Interfaces\Updateable');

        $mockContactDetailsService = m::mock('Common\Service\Data\Interfaces\Updateable');

        $mockPersonService = m::mock('Common\Service\Data\Interfaces\Updateable');

        // Expectations
        $environmentalComplaintRule->shouldReceive('validate')
            ->once()
            ->with($params['data'])
            ->andReturn($params['data']);

        $mockAddressEntity->shouldReceive('save')->with($params['address'])->andReturn(['id' => $addressId]);
        $this->sm->setService('Entity\Address', $mockAddressEntity);

        $mockContactDetailsService->shouldReceive('save')
            ->with($contactDetailsData)
            ->andReturn($contactDetailsId);

        $mockPersonService->shouldReceive('save')
            ->with($personData)
            ->andReturn($personId);

        $mockDataServiceManager
            ->shouldReceive('get')
            ->with('Generic\Service\Data\ContactDetails')
            ->andReturn($mockContactDetailsService);
        $mockDataServiceManager
            ->shouldReceive('get')
            ->with('Generic\Service\Data\Person')
            ->andReturn($mockPersonService);
        $this->sm->setService('DataServiceManager', $mockDataServiceManager);

        $mockComplaint = m::mock();
        $mockComplaint->shouldReceive('save')
            ->once()
            ->with(array_merge($params['data'], ['complainantContactDetails' => $contactDetailsId]))
            ->andReturn(['id' => $id]);
        $this->sm->setService('Entity\Complaint', $mockComplaint);

        $mockRestHelper->shouldReceive('makeRestCall')->once()->with('OcComplaint', 'DELETE', ['complaint' => $id]);
        $mockRestHelper->shouldReceive('makeRestCall')
            ->times(!empty($params['data']['ocComplaints']) ? 1 : 0)
            ->with(
                'OcComplaint',
                'POST',
                [
                    'complaint'=> $id,
                    'operatingCentre' => 1000
                ]
            );
        $mockRestHelper->shouldReceive('makeRestCall')
            ->times(!empty($params['data']['id']) ? 1 : 0)
            ->with(
                'Complaint',
                'GET',
                !empty($params['data']['id']) ? $params['data']['id'] : null,
                [
                    'children' => [
                        'complainantContactDetails' => [
                            'children' => [
                                'address',
                                'person' => [
                                    'forename',
                                    'familyName'
                                ]
                            ]
                        ]
                    ]
                ]
            )
            ->andReturn($existingData);
        $this->sm->setService('Helper\Rest', $mockRestHelper);

        $mockBusinessResponse = m::mock('\Common\BusinessService\Response');
        $mockBusinessResponse->shouldReceive('isOk')
            ->times(empty($params['id']) ? 1 : 0)
            ->andReturn(true);

        $mockTaskService->shouldReceive('process')
            ->times(empty($params['id']) ? 1 : 0)
            ->with($expectedTaskData)
            ->andReturn($mockBusinessResponse);

        $response = $this->sut->process($params);

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }

    public function processDataProvider()
    {
        return [
            // add
            [
                [
                    'caseId' => 111,
                    'data' => [
                        'urgent' => 'N',
                        'ocComplaints' => [
                            0 => 1000
                        ]
                    ],
                    'address' => 'someAddress',
                ],
                [
                    'caseId' => 111,
                ]
            ],
            // edit
            [
                [
                    'id' => 1,
                    'caseId' => 111,
                    'data' => [
                        'id' => 1,
                        'ocComplaints' => []
                    ],
                    'address' => 'someAddress',
                ],
                [
                    'caseId' => 111,
                ]
            ],
        ];
    }
}
