<?php

namespace Common\Service\Table\Formatter;

use Mockery;
use PHPUnit_Framework_TestCase;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\ServiceManager;

class VehicleRegistrationMarkTest extends PHPUnit_Framework_TestCase
{
    /** @var Mockery\MockInterface */
    private $mockServiceManager;

    protected function setUp()
    {
        $mockTranslator = Mockery::mock(TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with('application_vehicle-safety_vehicle.table.vrm.interim-marker')
            ->andReturn('TEST_INTERIM_TRANSLATION')
            ->getMock();
        $this->mockServiceManager = Mockery::mock(ServiceManager::class)
            ->shouldReceive('get')
            ->with('translator')
            ->andReturn($mockTranslator)
            ->getMock();
    }

    public function testThatNonInterimVrmIsDisplayed()
    {
        $data = [
            'vehicle' => ['vrm' => 'TEST_VRM'],
            'interimApplication' => null,
        ];
        $this->assertSame(
            'TEST_VRM',
            VehicleRegistrationMark::format($data, [], $this->mockServiceManager)
        );
    }

    public function testThatInterimVrmIsDisplayed()
    {
        $data = [
            'vehicle' => ['vrm' => 'TEST_VRM'],
            'interimApplication' => ['SOME_TEST_DATA'],
        ];
        $this->assertSame(
            'TEST_VRM (TEST_INTERIM_TRANSLATION)',
            VehicleRegistrationMark::format($data, [], $this->mockServiceManager)
        );
    }
}
