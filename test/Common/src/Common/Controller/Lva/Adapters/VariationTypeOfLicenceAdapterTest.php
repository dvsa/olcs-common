<?php

/**
 * Variation Type Of Licence Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Controller\Lva\Adapters\VariationTypeOfLicenceAdapter;
use CommonTest\Bootstrap;

/**
 * Variation Type Of Licence Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTypeOfLicenceAdapterTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected function setUp()
    {
        $this->sut = new VariationTypeOfLicenceAdapter();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testAlterForm()
    {
        $appId = 3;
        $licId = 5;
        $applicationType = 'internal';
        $stubbedTolData = [
            'licenceType' => 'xxx'
        ];

        $mockAppEntService = m::mock();
        $mockLicEntService = m::mock();
        $mockLicAdapter = m::mock();
        $mockFormHelper = m::mock();

        $this->sm->setService('Entity\Application', $mockAppEntService);
        $this->sm->setService('Entity\Licence', $mockLicEntService);
        $this->sm->setService('LicenceTypeOfLicenceAdapter', $mockLicAdapter);
        $this->sm->setService('Helper\Form', $mockFormHelper);

        $licTypeElement = m::mock();
        $form = m::mock('\Zend\Form\Form');
        $form->shouldReceive('get->get')
            ->andReturn($licTypeElement);

        $mockAppEntService->shouldReceive('getLicenceIdForApplication')
            ->with($appId)
            ->andReturn($licId);

        $mockLicAdapter->shouldReceive('alterForm')
            ->with($form, $licId, $applicationType)
            ->andReturn($form);

        $mockLicEntService->shouldReceive('getTypeOfLicenceData')
            ->with($licId)
            ->andReturn($stubbedTolData);

        $mockFormHelper->shouldReceive('setCurrentOption')
            ->with($licTypeElement, 'xxx');

        $this->assertSame($form, $this->sut->alterForm($form, $appId, $applicationType));
    }
}
