<?php

namespace CommonTest\Controller;

use Common\Mapper\GenericMapper;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the generic mapper
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class GenericMapperTest extends MockeryTestCase
{

    public function setUp()
    {
        $this->sut = new GenericMapper();

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
                ]
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
                'emailAddress' => $emailAddress
            ],
            'officeAddress' => [
                'postcode' => $postcode,
                'countryCode' => $countryCodeId
            ]
        ];

        $this->sut->setFieldMap($fieldMap);

        $this->assertEquals($expectedOutput, $this->sut->formatDataForForm($input));
    }
}
