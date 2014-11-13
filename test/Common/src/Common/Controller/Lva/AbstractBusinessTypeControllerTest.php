<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractBusinessTypeControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractBusinessTypeController');
    }

    public function testGetIndexAction()
    {
        $this->mockRender();

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $oEntity = m::mock()
            ->shouldReceive('getType')
            ->with(12)
            ->andReturn(
                [
                    'version' => '1',
                    'type' => [
                        'id' => 'x'
                    ]
                ]
            )
            ->getMock();

        $this->setService('Entity\Organisation', $oEntity);

        $this->sut->indexAction();

        $this->assertEquals('business_type', $this->view);
    }

    public function testPostWithInvalidData()
    {
        $this->mockRender();

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->setPost();

        $this->sut->indexAction();

        $this->assertEquals('business_type', $this->view);
    }

    public function testPostWithValidData()
    {
        $this->disableCsrf();

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->setPost(
            [
                'version' => 1,
                'data' => [
                    'type' => 'org_t_rc'
                ]
            ]
        );

        $oEntity = m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'version' => 1,
                    'id' => 12,
                    'type' => 'org_t_rc'
                ]
            )
            ->getMock();

        $this->setService('Entity\Organisation', $oEntity);

        $this->sut
            ->shouldReceive('postSave')
            ->with('business_type')
            ->shouldReceive('completeSection')
            ->with('business_type')
            ->andReturn('complete');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }
}
