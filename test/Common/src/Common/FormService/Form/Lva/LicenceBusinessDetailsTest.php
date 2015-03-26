<?php

/**
 * Licence Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Entity\OrganisationEntityService;
use Common\FormService\Form\Lva\LicenceBusinessDetails;

/**
 * Licence Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceBusinessDetailsTest extends MockeryTestCase
{
    protected $sut;

    protected $fsl;

    protected $formHelper;

    public function setUp()
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->fsl = m::mock('\Common\FormService\FormServiceManager')->makePartial();

        $this->sut = new LicenceBusinessDetails();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->fsl);
    }

    public function testAlterForm()
    {
        // Params
        $orgType = OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY;
        $orgId = 111;

        // Mocks
        $mockForm = m::mock();
        $mockLva = m::mock('\Common\FormService\FormServiceInterface');

        $this->fsl->setService('lva-licence', $mockLva);

        // Expectations
        $mockLva->shouldReceive('alterForm')
            ->with($mockForm);

        $this->formHelper->shouldReceive('createForm')
            ->with('Lva\BusinessDetails')
            ->andReturn($mockForm);

        $form = $this->sut->getForm($orgType, $orgId);

        $this->assertSame($mockForm, $form);
    }
}
