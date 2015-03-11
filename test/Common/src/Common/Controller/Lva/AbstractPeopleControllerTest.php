<?php

namespace CommonTest\Controller\Lva;

use Common\Service\Entity\OrganisationEntityService as Org;
use Mockery as m;

/**
 * Test Abstract People Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AbstractPeopleControllerTest extends AbstractLvaControllerTestCase
{
    private $adapter;

    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractPeopleController');

        $this->mockService('Script', 'loadFile')->with('lva-crud-delta');

        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');

        $this->sut->setAdapter($this->adapter);
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

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(123);

        $this->adapter->shouldReceive('alterFormForOrganisation')
            ->with($form, $table, 12)
            ->shouldReceive('addMessages')
            ->with(12, 123)
            ->shouldReceive('createTable')
            ->with(12)
            ->andReturn($table);

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

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(123);

        $this->adapter->shouldReceive('alterAddOrEditFormForOrganisation')
            ->with($form, 12)
            ->shouldReceive('addMessages')
            ->with(12, 123);

        $this->sut->indexAction();

        $this->assertEquals('person', $this->view);
    }

    public function testPostIndexActionWithValidDataForSaveSoleTrader()
    {
        $this->setPost([]);

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

        $this->sut
            ->shouldReceive('postSave')
            ->with('people')
            ->shouldReceive('completeSection')
            ->with('people');

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(123);

        $this->adapter->shouldReceive('alterAddOrEditFormForOrganisation')
            ->with($form, 12)
            ->shouldReceive('addMessages')
            ->with(12, 123)
            ->shouldReceive('save')
            ->with(
                12,
                [
                    'forename' => 'a',
                    'familyName' => 'b',
                    'birthDate' => '2014-01-01',
                    'version' => '',
                    'id' => '',
                    'title' => 'Mr'
                ]
            );

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

        $this->adapter->shouldReceive('canModify')
            ->with(12)
            ->andReturn(true)
            ->shouldReceive('alterAddOrEditFormForOrganisation')
            ->with($form, 12);

        $this->sut->addAction();
    }

    public function testAddActionWithoutPermission()
    {
        $this->mockOrganisationId(12);

        $this->adapter->shouldReceive('canModify')
            ->with(12)
            ->andReturn(false);

        $this->sut->shouldReceive('addErrorMessage')
            ->with('cannot-perform-action')
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('id')
            ->shouldReceive('getIdentifier')
            ->andReturn(1)
            ->shouldReceive('redirect->toRouteAjax')
            ->with(null, ['id' => 1]);

        $this->sut->addAction();
    }

    public function testDeleteActionWithPermission()
    {
        $this->mockOrganisationId(12);

        $this->adapter->shouldReceive('canModify')
            ->with(12)
            ->andReturn(true);

        $this->sut->shouldReceive('originalDeleteAction');

        $this->sut->deleteAction();
    }

    public function testDeleteActionWithoutPermission()
    {
        $this->mockOrganisationId(12);

        $this->adapter->shouldReceive('canModify')
            ->with(12)
            ->andReturn(false);

        $this->sut->shouldReceive('addErrorMessage')
            ->with('cannot-perform-action')
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('id')
            ->shouldReceive('getIdentifier')
            ->andReturn(1)
            ->shouldReceive('redirect->toRouteAjax')
            ->with(null, ['id' => 1]);

        $this->sut->deleteAction();
    }
}
