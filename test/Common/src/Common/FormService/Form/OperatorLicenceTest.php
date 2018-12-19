<?php
/**
 * Created by PhpStorm.
 * User: shaunhare
 * Date: 2018-12-17
 * Time: 10:00
 */

namespace CommonTest\FormService\Form;

use Common\FormService\Form\Licence\Surrender\OperatorLicence;
use Common\RefData;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use \Olcs\Form\Model\Form\Surrender\OperatorLicence as OperatorLicenceForm;

class OperatorLicenceTest extends TestCase
{
    private $sut;

    public function setUp()
    {
        $this->sut = new OperatorLicence();
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $fsm = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($fsm);
    }

    public function testGetForm()
    {
        $form = m::mock(\Common\Form\Form::class);

        $mockSubmit = m::mock();
        $this->formHelper->shouldReceive('createForm')->once()
            ->with(OperatorLicenceForm::class)
            ->andReturn($form);

        $formActions = m::mock(\Common\Form\Form::class);
        $formActions->shouldReceive('get')->with('submit')->andReturn(
            $mockSubmit
        );

        $mockSubmit->shouldReceive('setLabel')->once()->with('Save and continue');
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $form = $this->sut->getForm();
        $this->assertInstanceOf(\Common\Form\Form::class, $form);
    }

    /**
     * @dataProvider dpTestSetStatus
     */
    public function testSetStatus($status, $radioValue)
    {
        $form = m::mock(\Common\Form\Form::class);

        $apiData["licenceDocumentStatus"]["id"] = $status;

        $radioButtonObj = new \Common\Form\Elements\Types\Radio();

        $form->shouldReceive('get')->with('operatorLicenceDocument')->andReturnSelf();
        $form->shouldReceive('get')->with('licenceDocument')->andReturn($radioButtonObj);

        $this->sut->setStatus($form, $apiData);

        $this->assertEquals($radioButtonObj->getValue(), $radioValue);
    }

    public function dpTestSetStatus()
    {
        return [
          [
              'status' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
              'radio_value' => 'possession'
          ],
          [
              'status' => RefData::SURRENDER_DOC_STATUS_LOST,
              'radio_value' =>'lost'
          ],
          [
              'status' => RefData::SURRENDER_DOC_STATUS_STOLEN,
              'radio_value' => 'stolen'
          ]
        ];
    }
}
