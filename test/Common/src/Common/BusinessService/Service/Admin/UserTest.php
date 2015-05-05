<?php

/**
 * User Business Service Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace CommonTest\BusinessService\Service\Admin;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Admin\User;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * User Business Service Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class UserTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $dsm;

    protected $brm;

    protected $mockDataService;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new User();

        // mocks
        $mockDataServiceManager = m::mock();
        $this->dsm = $mockDataServiceManager;
        $this->mockDataService = m::mock('Common\Service\Data\User');
        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        // expectations
        $this->dsm->shouldReceive('get')->with('Common\Service\Data\User')->andReturn($this->mockDataService);

        // setup
        $this->sm->setService('DataServiceManager', $mockDataServiceManager);
        $this->sut->setBusinessRuleManager($this->brm);
        $this->sut->setServiceLocator($this->sm);

    }

    public function testProcessInvalidUserId()
    {
        // Params
        $userId = 101;
        $params = [
            'id' => $userId,
            'foo' => 'bar'
        ];

        // expectations
        $this->mockDataService->shouldReceive('getAllUserDetails')->with($userId)->andReturnNull();

        // test
        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_FAILED, $response->getType());
        $this->assertArrayHasKey('error', $response->getData());
    }

    public function testProcessUpdateUser()
    {
        $userId = 101;

        // Params
        $params = $this->getMockParams();
        $userDetails = $this->getMockUserDetails($userId);

        // setup mocks for business rules
        $mockLockedDateRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $mockBirthDateRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $mockPhoneContacts = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->brm->setService('LockedDate', $mockLockedDateRule);
        $this->brm->setService('BirthDate', $mockBirthDateRule);
        $this->brm->setService('PhoneContacts', $mockPhoneContacts);

        // expectations
        $this->mockDataService->shouldReceive('getAllUserDetails')->with($userId)->andReturn($userDetails);
        $mockLockedDateRule->shouldReceive('validate')->with($params['userLoginSecurity']['accountDisabled'])
            ->andReturnNull();
        $mockBirthDateRule->shouldReceive('validate')->with(m::type('array'))->andReturnNull();
        $mockPhoneContacts->shouldReceive('validate')->with(
            m::type('array'),
            m::type('string'),
            m::type('string')
        )->andReturnNull();
        $this->mockDataService->shouldReceive('saveUserRole')->with(m::type('array'))->andReturn($userId);

        // test
        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['id' => $userId], $response->getData());
    }

    public function testProcessCreateUser()
    {
        // Params
        $params = $this->getMockParams();

        // setup mocks for business rules
        $mockLockedDateRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $mockBirthDateRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $mockPhoneContacts = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->brm->setService('LockedDate', $mockLockedDateRule);
        $this->brm->setService('BirthDate', $mockBirthDateRule);
        $this->brm->setService('PhoneContacts', $mockPhoneContacts);

        // expectations
        $mockLockedDateRule->shouldReceive('validate')->with(
            $params['userLoginSecurity']['accountDisabled']
        )->andReturnNull();
        $mockBirthDateRule->shouldReceive('validate')->with(m::type('array'))->andReturnNull();
        $mockPhoneContacts->shouldReceive('validate')->with(
            m::type('array'),
            m::type('string'),
            m::type('string')
        )->andReturnNull();
        $this->mockDataService->shouldReceive('saveUserRole')->with(m::type('array'))->andReturn(123);

        // test
        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 123], $response->getData());
    }

    private function getMockParams()
    {
        return [
            'userType' => [
                'userType' => 'internal',
                'team' => '2',
                'application' => '',
                'transportManager' => '',
                'localAuthority' => '',
                'licenceNumber' => '',
                'partnerContactDetails' => '',
                'roles' => [
                    0 => '2',
                ],
            ],
            'userPersonal' => [
                'forename' => 'John',
                'familyName' => 'Smith',
                'birthDate' => '2012-03-02',
            ],
            'userContactDetails' => [
                'emailAddress' => 'test@foobar.com',
                'emailConfirm' => 'test@foobar.com',
                'phone' => '01234567890',
                'fax' => '1234',
            ],
            'address' => [
                'searchPostcode' => [
                    'postcode' => 'AB12DGE',
                ],
                'addressLine1' => 'a1',
                'addressLine2' => 'a2',
                'addressLine3' => 'a3',
                'addressLine4' => 'a4',
                'town' => 'Anytown',
                'postcode' => 'AB12DGE',
                'countryCode' => 'GB',
                'id' => '',
                'version' => '',
            ],
            'userLoginSecurity' => [
                'loginId' => 'testuser1',
                'memorableWord' => 'mem1',
                'lastSuccessfulLogin' => null,
                'attempts' => null,
                'resetPasswordExpiryDate' => null,
                'lockedDate' => null,
                'mustResetPassword' => 'N',
                'accountDisabled' => 'Y',
            ],
            'contactDetailsType' => 'ct_obj',
            'id' => '',
            'version' => '',
            'form-actions[continue]' => null,
            'form-actions' => [
                'submit' => '',
                'cancel' => null,
            ]
        ];
    }

    private function getMockUserDetails($userId = 1)
    {
        return [
            'id' => $userId,
            'version' => 2,
            'loginId' => 'l',
            'memorableWord' => 'mem',
            'hintQuestion1' => 1,
            'hintQuestion2' => 2,
            'hintAnswer1' => 'ans1',
            'hintAnswer2' => 'ans2',
            'mustRestPassword' => 'Y',
            'accountDisabled' => 'Y',
            'lockedDate' => '2015-01-01',
            'team' => 'test_team',
            'transportManager' => [
                'id' => 3
            ],
            'userRoles' => [
                0 => [
                    'role' => [
                        'id' => 99
                    ]
                ]
            ],
            'contactDetails' => [
                'id' => 111,
                'emailAddress' => 'someone@somewhere.com',
                'person' => [
                    'id' => 243,
                    'forename' => 'John',
                    'familyName' => 'Smith',
                    'birthDate' => ['1970-05-04']
                ],
                'address' => [
                    'id' => 244,
                    'addressLine1' => 'foo',
                    'postcode' => 'AB1 2CD'
                ],
                'phoneContacts' => [
                    0 => [
                        'phoneNumber' => '12345',
                        'phoneContactType' => [
                            'id' => 'phone_t_tel',
                        ]
                    ],
                    1 => [
                        'phoneNumber' => '54321',
                        'phoneContactType' => [
                            'id' => 'phone_t_fax',
                        ]
                    ]
                ]
            ],
            'lastSuccessfulLoginDate' => '2015-04-07 12:54:23',
            'attempts' => 2,
            'lockedDate' => '2015-06-07 17:00:00',
            'mustResetPassword' => 'Y',
            'resetPasswordExpiryDate' => '2015-01-02 19:00:00'
        ];
    }
}
