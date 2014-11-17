<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

/**
 * Test Abstract Community Licences Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractCommunityLicencesControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractCommunityLicencesController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\CommunityLicences');

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('community_licences', $this->view);
    }

    public function testPostWithValidData()
    {
        $this->setPost();
        $this->sut->shouldReceive('postSave')
            ->with('community_licences')
            ->shouldReceive('completeSection')
            ->with('community_licences')
            ->andReturn('complete');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }
}
