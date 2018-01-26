<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\TransportManagerDateOfBirth;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Entity\TransportManagerApplicationEntityService;

class TransportManagerDateOfBirthTest extends MockeryTestCase
{
    /** @var TransportManagerDateOfBirth */
    private $sut;

    /* @var \Mockery\MockInterface */
    private $sm;

    /* @var \Mockery\MockInterface */
    private $mockUrlHelper;

    public function setUp()
    {
        $this->sut = new TransportManagerDateOfBirth();

        $this->sm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $this->sm->shouldReceive('get')->with('Helper\Url')->andReturn($this->mockUrlHelper);
    }

    protected function mockGetStatusHtml($expectedStatusId, $expectedStatusDescription, $statusHtml = '<STATUS HTML>')
    {
        $mockViewHelperManager = m::mock();
        $mockViewHelper = m::mock();

        $this->sm->shouldReceive('get')
            ->with('ViewHelperManager')
            ->once()
            ->andReturn($mockViewHelperManager);

        $mockViewHelperManager->shouldReceive('get')
            ->with('transportManagerApplicationStatus')
            ->once()
            ->andReturn($mockViewHelper);

        $mockViewHelper->shouldReceive('render')
            ->with($expectedStatusId, $expectedStatusDescription)
            ->once()
            ->andReturn($statusHtml);
    }

    /**
     * Provider for testFormat
     */
    public function providerFormat()
    {
        return array(
            array( // NoLvaLocation
                array
                (
                    'data' => [
                        'dob' => '1980-12-01'
                    ],
                    'column' => ['name' => 'dob']
                ),
                '01/12/1980'
            ),
            array( // ApplicationInternal
                array
                (
                    'data' => [
                        'dob' => '1980-12-01',
                        'status' => [
                            'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                            'description' => 'status description',
                        ]
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'application',
                        'internal' => true,
                    ]
                ),
                '01/12/1980 <STATUS HTML>'
            ),
            array( // ApplicationExternal
                array
                (
                    'data' => [
                        'dob' => '1980-12-01',
                        'status' => [
                            'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                            'description' => 'status description',
                        ]
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'application',
                        'internal' => false,
                    ]
                ),
                '01/12/1980 <STATUS HTML>'
            ),
            array( // VariationInternal
                array
                (
                    'data' => [
                        'dob' => '1980-12-01',
                        'status' => [
                            'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                            'description' => 'status description',
                        ],
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'variation',
                        'internal' => true,
                    ]
                ),
                '01/12/1980 <STATUS HTML>'
            ),
            array( // VariationExternal
                array
                (
                    'data' => [
                        'dob' => '1980-12-01',
                        'status' => [
                            'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                            'description' => 'status description',
                        ],
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'variation',
                        'internal' => false,
                    ]
                ),
                '01/12/1980 <STATUS HTML>'
            ),
            array( // LicenceInternal
                array
                (
                    'data' => [
                        'dob' => '1980-12-01'
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'licence',
                        'internal' => true,
                    ]
                ),
                '01/12/1980'
            ),
            array( // LicenceExternal
                array
                (
                    'data' => [
                        'dob' => '1980-12-01'
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'licence',
                        'internal' => false,
                    ]
                ),
                '01/12/1980'
            )
        );
    }

    /**
     * @dataProvider providerFormat
     */
    public function testFormat($testData, $expected)
    {
        if (isset($testData['data']['status'])) {
            $this->mockGetStatusHtml($testData['data']['status']['id'], $testData['data']['status']['description']);
        }

        $this->assertEquals($expected, $this->sut->format($testData['data'], $testData['column'], $this->sm));
    }
}
