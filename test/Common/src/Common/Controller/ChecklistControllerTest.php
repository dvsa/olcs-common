<?php

namespace CommonTest\Controller\Lva;

use Common\RefData;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\LicenceChecklist;
use Hamcrest\Core\IsEqual;
use Mockery as m;
use CommonTest\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\TestHelpers\Controller\Traits\ControllerTestTrait;

class ChecklistControllerTest extends MockeryTestCase
{
    use ControllerTestTrait;

    protected function getServiceManager()
    {
        return Bootstrap::getServiceManager();
    }

    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Continuation\ChecklistController');

        $this->mockService('Helper\Translation', 'translate')
            ->andReturnUsing(
                function ($input) {
                    return $input;
                }
            );

        $mockFormServiceManager = m::mock();
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
    }

    public function testUsersAction()
    {
        $continuationId = 99;

        $this->sut->shouldReceive('params')->with('continuationDetailId')->once()->andReturn($continuationId);

        $this->sut->shouldReceive('handleQuery')
            ->once()
            ->with(IsEqual::equalTo(LicenceChecklist::create(['id' => $continuationId])))
            ->andReturn($this->aMockResultWithUserData());

        $view = $this->sut->usersAction();
        $this->assertEquals('layouts/simple', $view->getTemplate());

        $userSectionView = $view->getChildren()[0];
        $this->assertEquals('pages/continuation-section', $userSectionView->getTemplate());

        $expected = array (
            'licNo' => '1234',
            'data' =>
                array (
                    0 =>
                        array (
                            0 =>
                                array (
                                    'value' => 'continuations.users-section.table.name',
                                    'header' => true,
                                ),
                            1 =>
                                array (
                                    'value' => 'continuations.users-section.table.email',
                                    'header' => true,
                                ),
                            2 =>
                                array (
                                    'value' => 'continuations.users-section.table.permission',
                                    'header' => true,
                                ),
                        ),
                    1 =>
                        array (
                            0 =>
                                array (
                                    'value' => 'Test1 Test1',
                                ),
                            1 =>
                                array (
                                    'value' => 'test1@test.com',
                                ),
                            2 =>
                                array (
                                    'value' => 'role.operator',
                                ),
                        ),
                    2 =>
                        array (
                            0 =>
                                array (
                                    'value' => 'Test2 Test2',
                                ),
                            1 =>
                                array (
                                    'value' => 'test2@test.com',
                                ),
                            2 =>
                                array (
                                    'value' => 'role.operator',
                                ),
                        ),
                ),
            'totalMessage' => 'continuations.users-section-header',
            'totalCount' => 2,
        );

        $this->assertEquals($expected, $userSectionView->getVariables());
    }

    /**
     * @return m\LegacyMockInterface|m\MockInterface
     */
    private function aMockResultWithUserData()
    {
        return m::mock()->shouldReceive('isOk')->andReturn(true)->getMock()->shouldReceive('getResult')->andReturn(
            [
                'licence' =>
                    [
                        'licNo' => '1234',
                        'organisation' =>
                            [
                                'organisationUsers' =>
                                    [
                                        [
                                            'user' =>
                                                [
                                                    'contactDetails' =>
                                                        [
                                                            'emailAddress' => 'test1@test.com',
                                                            'person' =>
                                                                [
                                                                    'familyName' => 'Test1',
                                                                    'forename' => 'Test1',
                                                                ],
                                                        ],
                                                    'id' => 543,
                                                    'roles' =>
                                                        [
                                                            [
                                                                'description' => 'Operator',
                                                                'id' => 27,
                                                                'role' => 'operator',
                                                            ],
                                                        ],
                                                ]
                                        ],
                                        [
                                            'user' =>
                                                [
                                                    'contactDetails' =>
                                                        [
                                                            'emailAddress' => 'test2@test.com',
                                                            'person' =>
                                                                [
                                                                    'familyName' => 'Test2',
                                                                    'forename' => 'Test2',
                                                                ],
                                                        ],
                                                    'id' => 544,
                                                    'roles' =>
                                                        [
                                                            [
                                                                'description' => 'Operator',
                                                                'id' => 27,
                                                                'role' => 'operator',
                                                            ],
                                                        ],

                                                ]
                                        ]
                                    ]
                            ]
                    ],

            ]
        )->getMock();
    }
}
