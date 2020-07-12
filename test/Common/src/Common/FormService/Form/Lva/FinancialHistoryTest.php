<?php

/**
 * Financial History Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace CommonTest\FormService\Form\Lva;

use Common\Form\Elements\InputFilters\SingleCheckbox;
use Common\Form\Elements\Types\HtmlTranslated;
use Common\Form\Form;
use Common\FormService\Form\Lva\FinancialHistory;
use Common\FormService\FormServiceManager;
use Common\RefData;
use Common\Service\Helper\FormHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\ElementInterface;
use Zend\Form\Fieldset;
use Zend\Http\Request;

/**
 * Financial History Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialHistoryTest extends MockeryTestCase
{
    /** @var  FinancialHistory */
    protected $sut;

    /** @var FormHelperService|m\Mock */
    protected $formHelper;

    /** @var FormServiceManager|m\Mock */
    protected $fsm;

    public function setUp(): void
    {
        $this->formHelper = m::mock(FormHelperService::class);
        $this->fsm = m::mock(FormServiceManager::class)->makePartial();

        $this->sut = new FinancialHistory();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->fsm);
    }

    public function testGetForm()
    {
        /** @var Request|m\Mock $request */
        $request = m::mock(Request::class);

        // Mocks
        $mockForm = m::mock(Form::class);

        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\FinancialHistory', $request)
            ->andReturn($mockForm);

        $form = $this->sut->getForm($request, []);

        $this->assertSame($mockForm, $form);
    }

    /**
     * @dataProvider lvaDataProvider
     *
     * @param $lva
     */
    public function testGetFormWithNiFlagSetToY($lva)
    {
        /** @var Request|m\Mock $request */
        $request = m::mock(Request::class);

        // Mocks
        $mockConfirmationLabel = m::mock(SingleCheckbox::class);
        $mockConfirmationLabel->shouldReceive('setLabel')
            ->with('application_previous-history_financial-history.insolvencyConfirmation.title.ni')
            ->andReturnSelf();

        $mockDataFieldset = m::mock(Fieldset::class);
        $mockDataFieldset->shouldReceive('get')->with('financialHistoryConfirmation')->andReturn(
            m::mock()->shouldReceive('get')->with('insolvencyConfirmation')->andReturn($mockConfirmationLabel)
                ->getMock()
        );

        $mockForm = m::mock(Form::class);
        $mockForm->shouldReceive('get')
            ->with('data')
            ->andReturn($mockDataFieldset);

        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\FinancialHistory', $request)
            ->andReturn($mockForm);

        $form = $this->sut->getForm(
            $request,
            [
                'lva' => $lva,
                'niFlag' => 'Y',
            ]
        );

        $this->assertSame($mockForm, $form);
    }

    /**
     * @return array
     */
    public function lvaDataProvider()
    {
        return [
            ['variation'],
            ['application'],
        ];
    }

    /**
     * @dataProvider provideDirectorChangeWordingVariations
     *
     * @param string $organisationType one of RefData::ORG_TYPE_*
     * @param string $personDescription the type of person ("Person", "Director", "Partner")
     */
    public function testGetFormForDirectorChange($organisationType, $personDescription)
    {
        /** @var Request|m\Mock $request */
        $request = m::mock(Request::class);

        /** @var Form|m\Mock $mockForm */
        $mockForm = m::mock(Form::class);

        /** @var Fieldset|m\Mock $mockDataFieldset */
        $mockDataFieldset = m::mock(Fieldset::class);

        /** @var HtmlTranslated|m\Mock $mockHasAnyPersonElement */
        $mockHasAnyPersonElement = m::mock(ElementInterface::class);

        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\FinancialHistory', $request)
            ->andReturn($mockForm);

        $this->formHelper->shouldReceive('remove')->once()->with($mockForm, 'data->financeHint');
        $this->formHelper->shouldReceive('remove')->once()->with($mockForm, 'data->financialHistoryConfirmation');

        $mockForm->shouldReceive('get')->with('data')->andReturn($mockDataFieldset);
        $mockDataFieldset->shouldReceive('get')->with('hasAnyPerson')->andReturn($mockHasAnyPersonElement);
        $mockHasAnyPersonElement
            ->shouldReceive('setTokens')
            ->with([sprintf('Have any of the new %s been:', $personDescription)])
            ->once();

        $form = $this->sut->getForm(
            $request,
            ['variationType' => RefData::VARIATION_TYPE_DIRECTOR_CHANGE, 'organisationType' => $organisationType]
        );

        $this->assertSame($mockForm, $form);
    }

    public function provideDirectorChangeWordingVariations()
    {
        return [
            [RefData::ORG_TYPE_REGISTERED_COMPANY, 'directors'],
            [RefData::ORG_TYPE_SOLE_TRADER, 'people'],
            [RefData::ORG_TYPE_LLP, 'partners'],
            [RefData::ORG_TYPE_PARTNERSHIP, 'partners'],
            [RefData::ORG_TYPE_OTHER, 'people'],
            [RefData::ORG_TYPE_IRFO, 'people'],
            ['anything-else', 'people'],
        ];
    }
}
