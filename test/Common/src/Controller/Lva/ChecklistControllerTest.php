<?php

namespace CommonTest\Controller\Lva;

use Common\Controller\Continuation\ChecklistController;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\LicenceChecklist;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Hamcrest\Core\IsEqual;
use Mockery as m;
use CommonTest\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ZfcRbac\Service\AuthorizationService;

class ChecklistControllerTest extends MockeryTestCase
{
    protected function getServiceManager()
    {
        return Bootstrap::getServiceManager();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->mockNiTextTranslationUtil = m::mock(NiTextTranslation::class);
        $this->mockAuthService = m::mock(AuthorizationService::class);
        $this->mockFormServiceManager = m::mock(FormServiceManager::class);
        $this->mockTranslationHelper = m::mock(TranslationHelperService::class);

        $this->mockController(ChecklistController::class, [
            $this->mockNiTextTranslationUtil,
            $this->mockAuthService,
            $this->mockFormServiceManager,
            $this->mockTranslationHelper,
        ]);

        $this->mockTranslationHelper->shouldReceive('translate')
            ->andReturnUsing(
                function ($input) {
                    return $input;
                }
            );
    }

    protected function mockController($className, array $constructorParams = [])
    {
        $this->request = m::mock('\Laminas\Http\Request')->makePartial();

        // If constructor params are provided, pass them to the mock, otherwise mock without them
        if (!empty($constructorParams)) {
            $this->sut = m::mock($className, $constructorParams)
                ->makePartial()
                ->shouldAllowMockingProtectedMethods();
        } else {
            $this->sut = m::mock($className)
                ->makePartial()
                ->shouldAllowMockingProtectedMethods();
        }

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn($this->request);
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
