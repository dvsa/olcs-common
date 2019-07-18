<?php

namespace CommonTest\Service\Qa;

use Common\Form\Elements\InputFilters\QaRadio;
use Common\Service\Qa\RadioFactory;
use Common\Service\Qa\RadioFieldsetPopulator;
use Common\Service\Qa\TranslateableTextHandler;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;

/**
 * RadioFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RadioFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulate()
    {
        $valueOptions = [
            'permit_app_uc' => 'Under Consideration',
            'permit_app_nys' => 'Not Yet Submitted'
        ];

        $value = 'permit_app_uc';

        $notSelectedMessageOptions = [
            'key' => 'notSelectedMessageKey',
            'parameters' => [
                'notSelectedMessageParam1',
                'notSelectedMessageParam2'
            ],
        ];

        $translatedNotSelectedMessage = 'translatedNotSelectedMessage';

        $options = [
            'options' => $valueOptions,
            'value' => $value,
            'notSelectedMessage' => $notSelectedMessageOptions,
        ];

        $radio = m::mock(Radio::class);
        $radio->shouldReceive('setValueOptions')
            ->with($valueOptions)
            ->once();
        $radio->shouldReceive('setValue')
            ->with($value)
            ->once();
        $radio->shouldReceive('setOption')
            ->with('not_selected_message', $translatedNotSelectedMessage)
            ->once();

        $radioFactory = m::mock(RadioFactory::class);
        $radioFactory->shouldReceive('create')
            ->once()
            ->andReturn($radio);

        $translateableTextHandler = m::mock(TranslateableTextHandler::class);
        $translateableTextHandler->shouldReceive('translate')
            ->with($notSelectedMessageOptions)
            ->andReturn($translatedNotSelectedMessage);

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('add')
            ->with($radio)
            ->once();

        $sut = new RadioFieldsetPopulator($radioFactory, $translateableTextHandler);
        $sut->populate($fieldset, $options);
    }
}
