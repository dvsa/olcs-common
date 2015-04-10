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
}
