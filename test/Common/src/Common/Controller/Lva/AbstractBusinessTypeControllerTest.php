<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;

/**
 * Test Abstract Business Type Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractBusinessTypeControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractBusinessTypeController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\BusinessType');

        $form->shouldReceive('setData')
            ->andReturn($form);

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->mockEntity('Organisation', 'getType')
            ->with(12)
            ->andReturn(
                [
                    'version' => '1',
                    'type' => [
                        'id' => 'x'
                    ]
                ]
            );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('business_type', $this->view);
    }

    public function testPostWithInvalidData()
    {
        $form = $this->createMockForm('Lva\BusinessType');

        $form->shouldReceive('setData')
            ->with([])
            ->andReturn($form);

        $form->shouldReceive('isValid')
            ->andReturn(false);

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->setPost();

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('business_type', $this->view);
    }

    public function testPostWithValidData()
    {
        $form = $this->createMockForm('Lva\BusinessType');

        $form->shouldReceive('setData')
            ->andReturn($form);

        $form->shouldReceive('isValid')
            ->andReturn(true);

        $this->setPost(
            [
                'version' => 1,
                'data' => [
                    'type' => 'org_t_rc'
                ]
            ]
        );

        $this->mockEntity('Organisation', 'save')
            ->with(
                [
                    'version' => 1,
                    'id' => 12,
                    'type' => 'org_t_rc'
                ]
            );

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

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
