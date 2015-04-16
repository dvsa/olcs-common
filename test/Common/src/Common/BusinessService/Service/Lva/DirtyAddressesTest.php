<?php

/**
 * Dirty Addresses Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\DirtyAddresses;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Dirty Addresses Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DirtyAddressesTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new DirtyAddresses();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        $updated = [
            'correspondence' => [
                'fao' => 'Tim'
            ],
            'correspondence_address' => [
                'addressLine1' => 'Line 1',
                'addressLine2' => 'Line 2',
                'addressLine3' => 'Line 3',
                'addressLine4' => 'Line 4',
                'postcode' => 'LS1 1DD',
                'town' => 'Leeds',
                'countryCode' => 'GB'
            ],
            'contact' => [
                'email' => 'Line 1',
                'phone_business' => null,
                'phone_home' => '0113 123 1234',
                'phone_mobile' => null,
                'phone_fax' => null
            ]
        ];

        $original = [
            'correspondence' => [
                'fao' => 'Bob'
            ],
            'correspondence_address' => [
                'addressLine1' => 'Line 1',
                'addressLine2' => 'Line 2',
                'addressLine3' => 'Line 3',
                'addressLine4' => 'Line 4',
                'postcode' => 'LS1 1DD',
                'town' => 'Leeds',
                'countryCode' => 'GB'
            ],
            'contact' => [
                'email' => 'Line 1',
                'phone_business' => null,
                'phone_home' => '0116 123 4321',
                'phone_mobile' => null,
                'phone_fax' => null
            ]
        ];

        $this->sm->setService(
            'Helper\Data',
            new \Common\Service\Helper\DataHelperService()
        );

        $response = $this->sut->process(
            [
                'updated' => $updated,
                'original' => $original
            ]
        );

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(2, $response->getData()['dirtyFieldsets']);
    }
}
