<?php

/**
 * CPMS Identity Provider Test
 *
 * @author Nick Payne
 */
namespace CommonTest\Service\Processing;

use CommonTest\Bootstrap;
use Common\Service\Cpms\IdentityProviderFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * CPMS Identity Provider Test
 *
 * @author Nick Payne
 */
class IdentityProviderFactoryTest extends MockeryTestCase
{
    private function createService($config)
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $sm->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $sut = new IdentityProviderFactory();
        return $sut->createService($sm);
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing required CPMS configuration
     */
    public function testCreateServiceWithMissingCredentials()
    {
        $this->createService([]);
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing required option cpms.user_id
     */
    public function testCreateServiceWithMissingUserId()
    {
        $this->createService(
            [
                'cpms_credentials' => []
            ]
        );
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing required option cpms.client_id
     */
    public function testCreateServiceWithMissingClientId()
    {
        $this->createService(
            [
                'cpms_credentials' => [
                    'user_id' => 1234
                ]
            ]
        );
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing required option cpms.client_secret
     */
    public function testCreateServiceWithMissingClientSecret()
    {
        $this->createService(
            [
                'cpms_credentials' => [
                    'user_id'   => 1234,
                    'client_id' => 4321
                ]
            ]
        );
    }

    public function testCreateServiceWithValidCredentials()
    {
        $service = $this->createService(
            [
                'cpms_credentials' => [
                    'user_id'   => 1234,
                    'client_id' => 4321,
                    'client_secret' => 'secret'
                ]
            ]
        );

        $this->assertEquals(1234, $service->getUserId());
        $this->assertEquals(4321, $service->getClientId());
        $this->assertEquals('secret', $service->getClientSecret());
    }
}
