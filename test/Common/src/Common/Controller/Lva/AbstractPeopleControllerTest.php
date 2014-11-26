<?php

namespace CommonTest\Controller\Lva;

use Common\Service\Entity\OrganisationEntityService as Org;
use \Mockery as m;

/**
 * Test Abstract People Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
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

        $form->shouldReceive('setData');

        $this->mockRender();

        $this->mockOrganisationId(12);

        $this->mockEntity('Organisation', 'getType')
            ->with(12)
            ->andReturn(
                [
                    'type' => [
                        'id' => Org::ORG_TYPE_REGISTERED_COMPANY
                    ]
                ]
            );

        $this->mockEntity('Person', 'getAllForOrganisation')
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

        $this->mockService('Table', 'prepareTable')
            ->with('lva-people', $people)
            ->andReturn($table);

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

    public function testGetIndexActionForSoleTrader()
    {
        $form = $this->createMockForm('Lva\SoleTrader');

        $form->shouldReceive('setData')
            ->andReturn($form);

        $this->mockRender();

        $this->mockOrganisationId(12);

        $this->mockEntity('Organisation', 'getType')
            ->with(12)
            ->andReturn(
                [
                    'type' => [
                        'id' => Org::ORG_TYPE_SOLE_TRADER
                    ]
                ]
            );

        $this->mockEntity('Person', 'getFirstForOrganisation')
            ->with(12)
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
            );

        $this->sut->indexAction();

        $this->assertEquals('person', $this->view);
    }
}
