<?php

/**
 * Addresses Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\Addresses;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Helper\FormHelperService;

/**
 * Addresses Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AddressesTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');

        $this->sut = new Addresses();
        $this->sut->setFormHelper($this->formHelper);
    }

    public function testAlterFormWithDisallowedLicenceType()
    {
        $form = m::mock('\Zend\Form\Form');

        $this->formHelper->shouldReceive('createForm')
            ->with('Lva\Addresses')
            ->andReturn($form)
            ->shouldReceive('remove')
            ->with($form, 'establishment')
            ->andReturnSelf()
            ->shouldReceive('remove')
            ->with($form, 'establishment_address');

        $this->assertEquals(
            $form,
            $this->sut->getForm(LicenceEntityService::LICENCE_TYPE_RESTRICTED)
        );
    }
}
