<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractTypeOfLicenceControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractTypeOfLicenceController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\TypeOfLicence');

        $this->sut
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'version' => 1,
                    'niFlag' => 'x',
                    'goodsOrPsv' => 'y',
                    'licenceType' => 'z'
                ]
            );

        $form->shouldReceive('setData')
            ->with(
                [
                    'version' => 1,
                    'type-of-licence' => [
                        'operator-location' => 'x',
                        'operator-type' => 'y',
                        'licence-type' => 'z'
                    ]
                ]
            );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('type_of_licence', $this->view);
    }

    public function testPostWithInvalidData()
    {
        $form = $this->createMockForm('Lva\TypeOfLicence');

        $this->setPost();

        $form->shouldReceive('setData')
            ->with([])
            ->andReturn($form);

        $form->shouldReceive('isValid')
            ->andReturn(false);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('type_of_licence', $this->view);
    }

    public function testPostWithValidData()
    {
        $form = $this->createMockForm('Lva\TypeOfLicence');

        $this->setPost(
            [
                'version' => '',
                'type-of-licence' => [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ]
            ]
        );

        $form->shouldReceive('setData')
            ->andReturn($form);

        $form->shouldReceive('isValid')
            ->andReturn(true);

        $this->sut
            ->shouldReceive('getLicenceId')
            ->andReturn(7)
            ->shouldReceive('postSave')
            ->with('type_of_licence')
            ->shouldReceive('completeSection')
            ->with('type_of_licence')
            ->andReturn('complete');

        $lEntity = m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'id' => 7,
                    'version' => '',
                    'niFlag' => 'N',
                    'goodsOrPsv' => 'lcat_gv',
                    'licenceType' => 'ltyp_sn'
                ]
            )
            ->getMock();

        $this->setService('Entity\Licence', $lEntity);

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }
}
