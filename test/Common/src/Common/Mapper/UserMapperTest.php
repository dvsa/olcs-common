<?php

namespace CommonTest\Mapper;

use Common\Mapper\UserMapper;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the user mapper
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class UserMapperTest extends MockeryTestCase
{

    public function setUp()
    {
        $this->sut = new UserMapper();

        parent::setUp();
    }

    /**
     * Tests the mapper works correctly
     */
    public function testFormatDataForForm()
    {
        $birthDate = '1982-04-05';
        $emailAddress = 'example@example.com';
        $postcode = 'LA11 1DF';
        $countryCodeId = 'GB';
        $loginId = 'loggedinuser';
        $teamId = 2;
        $phoneNumber = 12345;
        $faxNumber = 23456;
        $mobileNumber = 34567; //included to demonstrate that it won't make it into the data

        $phoneContacts = [
            0 => [
                'phoneContactType' => [
                    'id' => 'phone_t_tel'
                ],
                'phoneNumber' => $phoneNumber
            ],
            1 => [
                'phoneContactType' => [
                    'id' => 'phone_t_fax'
                ],
                'phoneNumber' => $faxNumber
            ],
            2 => [
                'phoneContactType' => [
                    'id' => 'phone_t_mobile'
                ],
                'phoneNumber' => $mobileNumber
            ]
        ];

        $fieldMap = [
            'team||id' => 'userDetails',
            'loginId' => 'userDetails',
            'contactDetails||person||birthDate' => 'userDetails',
            'contactDetails||emailAddress' => 'userContact',
            'contactDetails||address||postcode' => 'officeAddress',
            'contactDetails||address||countryCode||id' => 'officeAddress',
            'contactDetails||nextArrayNotFound',
            'arrayNotFound||nextArrayNotFound'
        ];

        $input = [
            'unmappedField' => 'unmappedField',
            'loginId' => $loginId,
            'team' => [
                'id' => $teamId
            ],
            'contactDetails' => [
                'emailAddress' => $emailAddress,
                'person' => [
                    'birthDate' => $birthDate
                ],
                'address' => [
                    'postcode' => $postcode,
                    'countryCode' => [
                        'id' => $countryCodeId
                    ]
                ],
                'phoneContacts' => $phoneContacts
            ]
        ];

        $expectedOutput = [
            'unmappedField' => 'unmappedField',
            'userDetails' => [
                'team' => $teamId,
                'loginId' => $loginId,
                'birthDate' => $birthDate
            ],
            'userContact' => [
                'emailAddress' => $emailAddress,
                'emailConfirm' => $emailAddress,
                'phone' => $phoneNumber,
                'fax' => $faxNumber,
            ],
            'officeAddress' => [
                'postcode' => $postcode,
                'countryCode' => $countryCodeId
            ]
        ];

        $this->sut->setFieldMap($fieldMap);

        $this->assertEquals($expectedOutput, $this->sut->formatMyDetailsDataForForm($input, $fieldMap));
    }

    public function testFormatSave()
    {
        $result = $this->sut->formatSave($this->getFormData(), $this->getExpectedData());
        $this->assertEquals(99, $result['id']);
        $this->assertEquals(3, $result['version']);
        $this->assertNotEmpty($result['userRoles']);
        $this->assertNotEmpty($result['lockedDate']);
    }

    private function getFormData()
    {
        return [
            'userType' => [
                'userType' => 'internal',
                'team' => '2',
                'application' => '',
                'transportManager' => '',
                'localAuthority' => '',
                'licenceNumber' => '',
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

    private function getExpectedData()
    {
        return [
            'id' => 99,
            'version' => 3,
            'contactDetails' => [
                'id' => 100,
                'version' => 4,
                'person' => [
                    'id' => 5,
                    'version' => 6
                ],
                'phoneContacts' => [
                    0 => [
                        'phoneContactType' => 'phone_t_tel'
                    ],
                    1 => [
                        'phoneContactType' => 'phone_t_fax'
                    ]
                ]
            ]
        ];
    }
}
