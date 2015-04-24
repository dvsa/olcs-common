<?php

/**
 * Postcode Enforcement Area Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\PostcodeEnforcementAreaEntityService;
use Mockery as m;

/**
 * Postcode Enforcement Area Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PostcodeEnforcementAreaEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new PostcodeEnforcementAreaEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetEnforcementAreaByPostcodePrefix()
    {
        $prefix = 'LS9 6';

        $query = [
            'postcodeId' => $prefix,
        ];

        $response = [
            'Count' => 1,
            'Results' => [
                [
                    'postcodeId' => 'LS9 6',
                    'createdOn' => null,
                    'id' => 1488,
                    'lastModifiedOn' => null,
                    'version' => 1,
                    'enforcementArea' => [
                        'id' => 'V048',
                        'createdOn' => null,
                        'emailAddress' => 'foo@bar.com',
                        'lastModifiedOn' => null,
                        'name' => 'Leeds',
                        'version' => 1,
                    ],
                ],
            ],
        ];

        $this->expectOneRestCall('PostcodeEnforcementArea', 'GET', $query)
            ->will($this->returnValue($response));

        $expected = [
            'id' => 'V048',
            'createdOn' => null,
            'emailAddress' => 'foo@bar.com',
            'lastModifiedOn' => null,
            'name' => 'Leeds',
            'version' => 1,
        ];

        $this->assertSame($expected, $this->sut->getEnforcementAreaByPostcodePrefix($prefix));
    }


    /**
     * @group entity_services
     */
    public function testGetEnforcementAreaByPostcodePrefixNotFound()
    {
        $prefix = 'LS99 6';

        $query = [
            'postcodeId' => $prefix,
        ];

        $response = [
            'Count' => 0,
            'Results' => [],
        ];

        $this->expectOneRestCall('PostcodeEnforcementArea', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertNull($this->sut->getEnforcementAreaByPostcodePrefix($prefix));
    }
}
