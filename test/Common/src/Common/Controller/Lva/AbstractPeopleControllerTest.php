<?php

namespace CommonTest\Controller\Lva;

use Common\Service\Entity\OrganisationEntityService as Org;
use Mockery as m;
use CommonTest\Bootstrap;

/**
 * Test Abstract People Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AbstractPeopleControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractPeopleController');
    }

    /**
     * @todo These tests require a real service manager to run, as they are not mocking all dependencies,
     * these tests should be addresses
     */
    protected function getServiceManager()
    {
        return Bootstrap::getRealServiceManager();
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

    /**
     * @group abstractPeopleController
     */
    public function testGetIndexActionForSaveSoleTrader()
    {
        $form = $this->createMockForm('Lva\SoleTrader');

        $form->shouldReceive('setData')
            ->andReturn($form)
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(
                [
                    'data' => [
                        'forename' => 'a',
                        'familyName' => 'b',
                        'birthDate' => '2014-01-01',
                        'version' => '',
                        'id' => '',
                        'title' => 'Mr'
                    ]
                ]
            );

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
        $this->mockEntity('Person', 'save')
            ->andReturn(['id' => 1]);

        $this->mockEntity('OrganisationPerson', 'save');

        $this->request
            ->shouldReceive('isPost')
            ->andReturn(true);

        $this->sut
            ->shouldReceive('postSave')
            ->with('people')
            ->shouldReceive('completeSection')
            ->with('people');

        $this->sut->indexAction();
    }

    public function testBasicAddAction()
    {
        $form = $this->createMockForm('Lva\Person');

        $form->shouldReceive('setData')
            ->with([]);

        $this->mockOrganisationId(12);

        $this->mockRender();

        $this->mockEntity('Organisation', 'getType')
            ->with(12)
            ->andReturn(
                [
                    'type' => [
                        'id' => Org::ORG_TYPE_REGISTERED_COMPANY
                    ]
                ]
            );

        $this->getMockFormHelper()
            ->shouldReceive('remove')
            ->with($form, 'data->position');

        $this->sut->addAction();
    }
}
