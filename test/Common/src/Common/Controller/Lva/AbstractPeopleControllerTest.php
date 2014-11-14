<?php

namespace CommonTest\Controller\Lva;

use Common\Service\Entity\OrganisationEntityService as Org;
use \Mockery as m;

class AbstractPeopleControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractPeopleController');
    }

    public function testGetIndexActionForLimitedCompanyWithPeoplePopulated()
    {
        $form = $this->createMockForm('Lva\People');

        $form->shouldReceive('setData')
            ->with(
            );

        $this->mockRender();

        $this->mockOrganisationId(12);

        $this->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('getType')
            ->with(12)
            ->andReturn(
                [
                    'type' => [
                        'id' => Org::ORG_TYPE_REGISTERED_COMPANY
                    ]
                ]
            )
            ->getMock()
        );

        $this->setService(
            'Entity\Person',
            m::mock()
            ->shouldReceive('getAllForOrganisation')
            ->andReturn(
                [
                    'Count' => 1,
                    'Results' => [
                        [
                            'position' => 'x',
                            'person' => [
                                'x' => 'y'
                            ]
                        ]
                    ]
                ]
            )
            ->getMock()
        );

        $table = m::mock()
            ->shouldReceive('removeColumn')
            ->with('position')
            ->shouldReceive('setVariable')
            ->with('title', 'Directors')
            ->getMock();

        $people = [
            [
                'x' => 'y',
                'position' => 'x'
            ]
        ];

        $this->setService(
            'Table',
            m::mock()
            ->shouldReceive('prepareTable')
            ->with('lva-people', $people)
            ->andReturn($table)
            ->getMock()
        );

        $this->setService(
            'translator',
            m::mock()
            ->shouldReceive('translate')
            ->with('selfserve-app-subSection-your-business-people-tableHeaderDirectors')
            ->andReturn('Directors')
            ->shouldReceive('translate')
            ->with('selfserve-app-subSection-your-business-people-guidanceLC')
            ->andReturn('Guidance')
            ->getMock()
        );


        $element = m::mock()
            ->shouldReceive('setValue')
            ->with('Guidance')
            ->getMock();

        $fieldset = m::mock()
            ->shouldReceive('get')
            ->andReturn($element)
            ->getMock();

        $tableElement = m::mock()
            ->shouldReceive('setTable')
            ->with($table)
            ->getMock();

        $tableFieldset = m::mock()
            ->shouldReceive('get')
            ->andReturn($tableElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('guidance')
            ->andReturn($fieldset)
            ->shouldReceive('get')
            ->with('table')
            ->andReturn($tableFieldset);

        $this->sut->indexAction();

        $this->assertEquals('people', $this->view);
    }

    /*
    public function testPostWithInvalidData()
    {
    }

    public function testPostWithValidData()
    {
    }
     */
}
