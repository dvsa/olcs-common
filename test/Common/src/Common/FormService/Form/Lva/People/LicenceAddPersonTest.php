<?php
namespace CommonTest\FormService\Form\Lva\People;

use Common\Form\Model\Form\Licence\AddPerson;
use Common\FormService\Form\Lva\People\LicenceAddPerson as Sut;
use Common\Service\Helper\FormHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class LicenceAddPersonTest extends MockeryTestCase
{
    const TEST_ORGANISATION_TYPE = 'AOEOaedrTUIDAoeua';

    public function testGetForm()
    {
        $form = new AddPerson();

        $formHelper = m::mock(FormHelperService::class);
        $formHelper->shouldReceive('createForm')->once()
            ->with(AddPerson::class)
            ->andReturn($form);

        $sut = new Sut();
        $sut->setFormHelper($formHelper);

        $actual = $sut->getForm(['organisationType' => self::TEST_ORGANISATION_TYPE]);
        self::assertSame($form, $actual);
    }
}
