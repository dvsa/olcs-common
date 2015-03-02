<?php

/**
 * AbstractTrailersControllerTest.php
 */

namespace CommonTest\Controller\Lva;

use Mockery as m;

use CommonTest\Bootstrap;

/**
 * Test Abstract People Controller
 *
 * Josh Curtis <josh.curtis@valtech.co.uk>
 */
class AbstractTrailersControllerTest extends AbstractLvaControllerTestCase
{
    /**
     * Set up the test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractTrailersController');
        $this->mockService('Script', 'loadFile')->with('lva-crud');
    }

    /**
     * Test getting the licence trailers index action.
     */
    public function testGetIndexActionForLicenceTrailers()
    {
        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(1);

        $form = $this->createMockForm('Lva\Trailers');

        $this->mockRender();

        $results = array(
            array("id" => 1, "trailerNo" => "A", "specifiedDate" => "2014-01-01"),
            array("id" => 2, "trailerNo" => "B", "specifiedDate" => "2014-02-02"),
            array("id" => 3, "trailerNo" => "C", "specifiedDate" => "2014-03-03")
        );

        $this->mockEntity('Trailer', 'getTrailerDataForLicence')
             ->with(1)
             ->andReturn(array('Count' => 3, 'Results' => $results));

        $this->mockService('Table', 'prepareTable')
             ->with('lva-trailers', $results);

        $form->shouldReceive('get')
            ->with('table')
            ->andReturn(
                m::mock('ElementInterface')
                    ->shouldReceive('get')
                    ->with('table')
                    ->andReturn(
                        m::mock('Table')
                            ->shouldReceive('setTable')
                            ->getMock()
                    )->getMock()
            );

        $form->shouldReceive('get')
            ->with('guidance')
            ->andReturn(
                m::mock('ElementInterface')
                    ->shouldReceive('get')
                    ->with('guidance')
                    ->andReturn(
                        m::mock('Element')
                            ->shouldReceive('setValue')
                            ->getMock()
                    )->getMock());

        $this->setService(
            'translator',
            m::mock()
                ->shouldReceive('translate')
                ->with('licence_goods-trailers_trailer.table.guidance')
                ->getMock()
        );

        $this->sut->indexAction();
    }

    public function testPostIndexActionForLicenceTrailers()
    {
        $this->setPost(array());

        $this->sut
            ->shouldReceive('getCrudAction')
            ->andReturn('notnull')
            ->shouldReceive('handleCrudAction');

        $this->sut->indexAction();
    }

    public function testPostIndexSectionComplete()
    {
        $this->setPost(array());

        $this->sut
            ->shouldReceive('getCrudAction')
            ->andReturn(null)
            ->shouldReceive('completeSection')
            ->with('trailers');

        $this->sut->indexAction();
    }

    public function testAddAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('add');

        $this->sut->addAction();
    }

    public function testEditAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('edit');

        $this->sut->editAction();
    }

    public function testAddOrEditThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->sut->addOrEdit('invalid');
    }

    public function testAddOrEditAddGet()
    {
        $childId = 1;
        $data = array(
            "id" => 1, "trailerNo" => "A", "specifiedDate" => "2014-01-01"
        );

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(1);

        $form = $this->createMockForm('Lva\Trailer');

        $this->sut->shouldReceive('getRequest')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isPost')
                    ->andReturn(true)
                    ->shouldReceive('getPost')
                    ->andReturn($data)
                    ->getMock()
            );

        $form->shouldReceive('setData')
            ->with([])
            ->shouldReceive('isValid')
            ->andReturn(true);

        $this->mockRender();

        $this->sut->addOrEdit('add');
    }

    public function testAddOrEditAddPost()
    {
        $childId = 1;
        $data = array(
            "id" => 1, "trailerNo" => "A0001", "specifiedDate" => "2014-01-01"
        );

        $this->request
            ->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('isPost')
            ->andReturn(true);

        $form = $this->createMockForm('Lva\Trailer');
        $form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()->shouldReceive('remove')->with('addAnother')->getMock()
            );

        $this->sut->shouldReceive('params')->with('child_id')->andReturn($childId);
        $this->mockEntity('Trailer', 'getById')
            ->with($childId)
            ->andReturn([]);


        $form->shouldReceive('setData')
            ->with([])
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(array(
                'data' => array()
            ));

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(1);

        $this->mockEntity('Trailer', 'save')
            ->with(array(
                'licence' => 1,
                'specifiedDate' => ''
            ));

        $this->mockService('Helper\Date', 'getDate');

        $this->sut->shouldReceive('handlePostSave');

        $this->sut->addOrEdit('add');
    }

    public function testAddOrEditEditGet()
    {
        $childId = 1;
        $data = array(
            "id" => 1, "trailerNo" => "A0001", "specifiedDate" => "2014-01-01"
        );

        $this->request
            ->shouldReceive('isPost')
            ->andReturn(false)
            ->shouldReceive('isPost')
            ->andReturn(false);

        $form = $this->createMockForm('Lva\Trailer');
        $form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()->shouldReceive('remove')->with('addAnother')->getMock()
            );

        $this->sut->shouldReceive('params')->with('child_id')->andReturn($childId);
        $this->mockEntity('Trailer', 'getById')
            ->with($childId)
            ->andReturn([]);

        $form->shouldReceive('setData')
            ->with(array(
                'data' => array()
            ))
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(array(
                'data' => array()
            ));

        $this->mockRender();

        $this->sut->addOrEdit('edit');
    }


    public function testAddOrEditEditPost()
    {
        $childId = 1;
        $data = array(
            "id" => 1, "trailerNo" => "A0001", "specifiedDate" => "2014-01-01"
        );

        $this->request
            ->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('isPost')
            ->andReturn(true);

        $form = $this->createMockForm('Lva\Trailer');
        $form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()->shouldReceive('remove')->with('addAnother')->getMock()
            );

        $this->sut->shouldReceive('params')->with('child_id')->andReturn($childId);
        $this->mockEntity('Trailer', 'getById')
            ->with($childId)
            ->andReturn([]);


        $form->shouldReceive('setData')
            ->with([])
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(array(
                'data' => array()
            ));

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(1);

        $this->mockEntity('Trailer', 'save')
            ->with(array(
                'licence' => 1
            ));

        $this->sut->shouldReceive('handlePostSave');

        $this->sut->addOrEdit('edit');
    }

    public function testDelete()
    {
        $this->sut->shouldReceive('params')
            ->with('child_id');

        $this->mockEntity('Trailer', 'delete');

        $this->sut->delete();
    }
}
