<?php

/**
 * Abstract Type Of Licence Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Abstract Type Of Licence Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractTypeOfLicenceAdapterTest extends MockeryTestCase
{
    protected $sut;

    protected function setUp()
    {
        $this->sut = m::mock('\Common\Controller\Lva\Adapters\AbstractTypeOfLicenceAdapter')
            ->makePartial();
    }

    /**
     * @group abstract_type_of_licence_adapter
     */
    public function testGetConfirmationMessage()
    {
        $this->assertNull($this->sut->getConfirmationMessage());
    }

    /**
     * @group abstract_type_of_licence_adapter
     */
    public function testBetExtraConfirmationMessage()
    {
        $this->assertNull($this->sut->getExtraConfirmationMessage());
    }

    /**
     * @group abstract_type_of_licence_adapter
     */
    public function testGetQueryParams()
    {
        $this->assertEquals(['query' => []], $this->sut->getQueryParams());
    }

    /**
     * @group abstract_type_of_licence_adapter
     */
    public function testGetRouteParams()
    {
        $this->assertEquals(['action' => 'confirmation'], $this->sut->getRouteParams());
    }

    /**
     * @group abstract_type_of_licence_adapter
     */
    public function testAlterForm()
    {
        $id = null;
        $applicationType = null;
        $form = m::mock('\Zend\Form\Form');

        $this->assertSame($form, $this->sut->alterForm($form, $id, $applicationType));
    }

    /**
     * @group abstract_type_of_licence_adapter
     */
    public function testSetMessages()
    {
        $this->assertNull($this->sut->setMessages());
    }

    /**
     * @group abstract_type_of_licence_adapter
     */
    public function testProcessChange()
    {
        $this->assertFalse($this->sut->processChange([], []));
    }

    /**
     * @group abstract_type_of_licence_adapter
     */
    public function testProcessFirstSave()
    {
        $this->assertNull($this->sut->processFirstSave(3));
    }

    /**
     * @group abstract_type_of_licence_adapter
     */
    public function testIsCurrentDataSet()
    {
        $data = [
            'niFlag' => null,
            'goodsOrPsv' => null,
            'licenceType' => null
        ];

        $this->assertFalse($this->sut->isCurrentDataSet($data));
    }

    /**
     * @group abstract_type_of_licence_adapter
     */
    public function testIsCurrentDataSetWhenSet()
    {
        $data = [
            'niFlag' => 'x',
            'goodsOrPsv' => 'y',
            'licenceType' => 'z'
        ];

        $this->assertTrue($this->sut->isCurrentDataSet($data));
    }
}
