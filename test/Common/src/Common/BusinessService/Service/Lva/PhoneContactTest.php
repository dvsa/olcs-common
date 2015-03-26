<?php

/**
 * Phone Contact Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\PhoneContact;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Phone Contact Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PhoneContactTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new PhoneContact();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        $data = [
            'contact' => [
                // existing but no data; delete
                'phone_business_id' => 10,
                'phone_business_version' => 1,
                // existing with data, update
                'phone_home_id' => 20,
                'phone_home_version' => 1,
                'phone_home' => '0113 123 1234',
                // new with no data; no action
                'phone_mobile_id' => null,
                'phone_mobile_version' => null,
                'phone_mobile' => null,
                // new with no data; no action
                'phone_fax_id' => null,
                'phone_fax_version' => null,
                'phone_fax' => null
            ]
        ];

        $saveData = [
            'id' => 20,
            'version' => 1,
            'phoneNumber' => '0113 123 1234',
            'phoneContactType' => 'phone_t_home',
            'contactDetails' => 4
        ];

        $this->sm->setService(
            'Entity\PhoneContact',
            m::mock()
            ->shouldReceive('save')
            ->with($saveData)
            ->shouldReceive('delete')
            ->with(10)
            ->getMock()
        );

        $response = $this->sut->process(
            [
                'data' => $data,
                'correspondenceId' => 4
            ]
        );

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals([], $response->getData());
    }
}
