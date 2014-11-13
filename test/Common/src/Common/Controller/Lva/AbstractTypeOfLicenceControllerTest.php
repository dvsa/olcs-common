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
        $this->mockRender();

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
        $this->sut->indexAction();

        $this->assertEquals('type_of_licence', $this->view);
    }

    public function testPostWithInvalidData()
    {
        $this->mockRender();

        $this->setPost();

        $this->sut->indexAction();

        $this->assertEquals('type_of_licence', $this->view);
    }

    public function testPostWithValidData()
    {
        $this->disableCsrf();

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
