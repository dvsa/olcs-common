<?php

namespace CommonTest\Form\View\Helper;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Common\Form\InsufficientFinancesForm;
use Mockery as m;

/**
 * InsufficientFinancesFormTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class InsufficientFinancesFormTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var InsufficientFinancesForm
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new InsufficientFinancesForm();

        parent::setUp();
    }

    /**
     * @dataProvider dataProviderTestIsValid
     */
    public function testIsValid(
        $yesNoValue,
        $radioValue,
        $expectRadioRequired,
        $expectFileCountRequired,
        $expectYesNoSetMessage
    ) {
        $yesNoInput = m::mock();
        if ($expectYesNoSetMessage) {
            $yesNoInput->shouldReceive('setErrorMessage')->with('continuations.insufficient-finances.no')->once();
        }

        $fileCountInput = m::mock();
        $fileCountInput->shouldReceive('setRequired')->with($expectFileCountRequired)->once();
        $fileCountInput->shouldReceive('setErrorMessage')->with('continuations.insufficient-finances.upload-files')
            ->once();

        $radioInput = m::mock();
        $radioInput->shouldReceive('setRequired')->with($expectRadioRequired)->once();

        $this->initForm($radioValue, $yesNoValue, $yesNoInput, $fileCountInput, $radioInput);
        $this->sut->isValid();
    }

    public function dataProviderTestIsValid()
    {
        return [
            ['X', '', false, false, false],
            ['N', '', false, false, true],
            ['Y', '', true, false, false],
            ['Y', 'X', true, false, false],
            ['Y', 'upload', true, true, false],
            ['Y', 'send', true, false, false],
        ];
    }

    private function initForm($radioValue, $yesNoValue, $yesNoInput, $fileCountInput, $radioInput)
    {
        $insufficientFinancesFieldset = m::mock(Fieldset::class)->makePartial();
        $insufficientFinancesFieldset->setName('insufficientFinances');
        $insufficientFinancesFieldset->shouldReceive('get')->with('yesContent')->once()->andReturn(
            m::mock()->shouldReceive('get')->with('radio')->once()->andReturn(
                m::mock()->shouldReceive('getValue')->with()->once()->andReturn($radioValue)->getMock()
            )->getMock()
        );
        $insufficientFinancesFieldset->shouldReceive('get')->with('yesNo')->twice()->andReturn(
            m::mock()->shouldReceive('getValue')->with()->twice()->andReturn($yesNoValue)->getMock()
        );

        $this->sut->setData(['x' => 1]);
        $this->sut->setUseInputFilterDefaults(false);
        $this->sut->add($insufficientFinancesFieldset);

        $uploadContentInput = m::mock();
        $uploadContentInput->shouldReceive('get')->with('fileCount')->andReturn($fileCountInput);

        $yesContentInput = m::mock();
        $yesContentInput->shouldReceive('get')->with('radio')->andReturn($radioInput);
        $yesContentInput->shouldReceive('get')->with('uploadContent')->andReturn($uploadContentInput);

        $insufficientFinancesInput = m::mock();
        $insufficientFinancesInput->shouldReceive('get')->with('yesContent')->andReturn($yesContentInput);
        $insufficientFinancesInput->shouldReceive('get')->with('yesNo')->andReturn($yesNoInput);

        $inputFilter = m::mock(InputFilter::class)->makePartial();
        $inputFilter->shouldReceive('get')->with('insufficientFinances')->andReturn($insufficientFinancesInput);

        $this->sut->setInputFilter($inputFilter);
    }
}
