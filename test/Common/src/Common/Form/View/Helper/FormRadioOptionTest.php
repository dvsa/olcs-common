<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormRadioOption;
use Common\View\Helper\UniqidGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Laminas\Form\Element\Radio;

class FormRadioOptionTest extends TestCase
{
    public function testInvokeNull()
    {
        $sut = new FormRadioOption();
        $this->assertSame($sut, $sut->__invoke());
    }

    public function testInvoke()
    {
        $idGenerator = m::mock(UniqidGenerator::class);
        $idGenerator->shouldReceive('generateId')->twice()->andReturn('generated_id');
        $sut = new FormRadioOption($idGenerator);
        $radioElement = new Radio('NAME');
        $radioElement->setValueOptions(['A' => 'aaa', 'B' => 'bbb']);

        $rendered = $sut->__invoke($radioElement, 'B');
        $this->assertSame(
            '<div class="govuk-radios"><div class="govuk-radios__item"><input type="radio" name="NAME" class="govuk-radios__input" value="B" id="generated_id"><label class="govuk-label&#x20;govuk-radios__label" for="generated_id">bbb</label></div></div>',
            $rendered
        );
        $rendered = $sut->__invoke($radioElement, 'A');
        $this->assertSame(
            '<div class="govuk-radios"><div class="govuk-radios__item"><input type="radio" name="NAME" class="govuk-radios__input" value="A" id="generated_id"><label class="govuk-label&#x20;govuk-radios__label" for="generated_id">aaa</label></div></div>',
            $rendered
        );
    }
}
