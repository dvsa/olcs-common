<?php

namespace CommonTest\Service\Qa\Custom\Common;

use Common\Form\Elements\Types\Html;
use Common\Service\Qa\Custom\Common\WarningAdder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\View\Helper\Partial;

/**
 * WarningAdderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class WarningAdderTest extends MockeryTestCase
{
    const WARNING_KEY = 'warning.key';

    const PRIORITY = 25;

    const ELEMENT_NAME = 'xyzWarning';

    public function testAdd()
    {
        $warningMarkup = '<h1>warning markup</h1>';

        $partial = m::mock(Partial::class);
        $partial->shouldReceive('__invoke')
            ->with(
                'partials/warning-component',
                ['translationKey' => self::WARNING_KEY]
            )
            ->once()
            ->andReturn($warningMarkup);

        $warningElementParams = [
            'name' => self::ELEMENT_NAME,
            'type' => Html::class,
            'attributes' => [
                'value' => $warningMarkup
            ]
        ];

        $warningFlagsParams = ['priority' => self::PRIORITY];

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('add')
            ->with($warningElementParams, $warningFlagsParams)
            ->once();


        $warningAdder = new WarningAdder($partial);
        $warningAdder->add($fieldset, self::WARNING_KEY, self::PRIORITY, self::ELEMENT_NAME);
    }
}
