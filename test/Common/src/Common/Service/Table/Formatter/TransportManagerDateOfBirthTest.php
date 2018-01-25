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

    public function testFormatNoLvaLocation()
    {
        $data = [
            'dob' => '1980-12-01'
        ];
        $column = [
            'name' => 'dob'
        ];
        $expected = '01/12/1980';

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatApplicationInternal()
    {
        $data = [
            'dob' => '1980-12-01',
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ]
        ];
        $column = [
            'name' => 'dob',
            'lva' => 'application',
            'internal' => true,
        ];

        $expected = '01/12/1980 <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
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

    public function testFormatApplicationExternal()
    {
        $data = [
            'dob' => '1980-12-01',
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
        ];
        $column = [
            'name' => 'dob',
            'lva' => 'application',
            'internal' => false,
        ];
        $expected = '01/12/1980 <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatVariationInternal()
    {
        $data = [
            'dob' => '1980-12-01',
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
        ];
        $column = [
            'name' => 'dob',
            'lva' => 'variation',
            'internal' => true,
        ];
        $expected = '01/12/1980 <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatVariationInternalInvalidAction()
    {
        $data = [
            'dob' => '1980-12-01',
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
        ];
        $column = [
            'name' => 'dob',
            'lva' => 'variation',
            'internal' => true,
        ];
        $expected = '01/12/1980 <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatVariationExternal()
    {
        $data = [
            'dob' => '1980-12-01',
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
        ];
        $column = [
            'name' => 'dob',
            'lva' => 'variation',
            'internal' => false,
        ];
        $expected = '01/12/1980 <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatVariationExternalNoLink()
    {
        $data = [
            'dob' => '1980-12-01',
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
        ];
        $column = [
            'name' => 'dob',
            'lva' => 'variation',
            'internal' => false,
        ];
        $expected = '01/12/1980 <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatLicenceInternal()
    {
        $data = [
            'dob' => '1980-12-01'
        ];
        $column = [
            'name' => 'dob',
            'lva' => 'licence',
            'internal' => true,
        ];
        $expected = '01/12/1980';

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatLicenceExternal()
    {
        $data = [
            'dob' => '1980-12-01'
        ];
        $column = [
            'name' => 'dob',
            'lva' => 'licence',
            'internal' => false,
        ];
        $expected = '01/12/1980';

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }
}
