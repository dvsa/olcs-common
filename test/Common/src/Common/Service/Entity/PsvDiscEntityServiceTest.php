<?php

/**
 * PsvDisc Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\PsvDiscEntityService;
use Mockery as m;

/**
 * PsvDisc Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvDiscEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new PsvDiscEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testCeaseDiscs()
    {
        $ids = array(3, 7, 8);

        $date = date('Y-m-d');

        $this->mockDate($date);

        $data = array(
            array(
                'id' => 3,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            array(
                'id' => 7,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            array(
                'id' => 8,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            '_OPTIONS_' => array(
                'multiple' => true
            )
        );

        $this->expectOneRestCall('PsvDisc', 'PUT', $data);

        $this->sut->ceaseDiscs($ids);
    }

    /**
     * @group entity_services
     */
    public function testRequestDiscs()
    {
        $count = 3;
        $data = array(
            'foo' => 'bar'
        );

        $saveData = array(
            '_OPTIONS_' => array(
                'multiple' => true
            ),
            array(
                'foo' => 'bar',
                'isCopy' => 'N'
            ),
            array(
                'foo' => 'bar',
                'isCopy' => 'N'
            ),
            array(
                'foo' => 'bar',
                'isCopy' => 'N'
            )
        );

        $this->expectOneRestCall('PsvDisc', 'POST', $saveData);

        $this->sm->setService(
            'Helper\Data',
            // we could inject the real data helper here, but it's fairly trivial to mock it
            m::mock()
                ->shouldReceive('arrayRepeat')
                    ->with(['isCopy' => 'N', 'foo' => 'bar'], 3)
                    ->andReturn(
                        [
                            ['isCopy' => 'N', 'foo' => 'bar'],
                            ['isCopy' => 'N', 'foo' => 'bar'],
                            ['isCopy' => 'N', 'foo' => 'bar'],
                        ]
                    )
                ->getMock()
        );

        $this->sut->requestDiscs($count, $data);
    }

    /**
     * @group psvDiscEntityService
     */
    public function testGetNotCeasedDiscs()
    {
        $licenceId = 1;
        $query = [
            'ceasedDate' => 'NULL',
            'licence' => $licenceId,
            'limit' => 'all'
        ];

        $this->expectOneRestCall('PsvDisc', 'GET', $query);

        $this->sut->getNotCeasedDiscs($licenceId);
    }

    /**
     * @group entity_services
     */
    public function testRequestBlankDiscs()
    {
        $saveData = array(
            '_OPTIONS_' => array(
                'multiple' => true
            ),
            array(
                'licence' => 10,
                'ceasedDate' => null,
                'issuedDate' => null,
                'discNo' => null,
                'isCopy' => 'N'
            )
        );

        $this->expectOneRestCall('PsvDisc', 'POST', $saveData);

        $this->sm->setService('Helper\Data', new \Common\Service\Helper\DataHelperService());

        $this->sut->requestBlankDiscs(10, 1);
    }

    /**
     * @group entity_services
     */
    public function testUpdateExistingForLicence()
    {
        $licenceId = 1;
        $query = [
            'ceasedDate' => 'NULL',
            'licence' => $licenceId,
            'limit' => 'all'
        ];

        $this->expectedRestCallInOrder('PsvDisc', 'GET', $query)
            ->willReturn(
                [
                    'Results' => [
                        ['id' => 5],
                        ['id' => 10]
                    ],
                    'Count' => 2
                ]
            );

        $date = date('Y-m-d');

        $this->mockDate($date);

        $data = [
            [
                'id' => 5,
                'ceasedDate' => $date,
                '_OPTIONS_' => ['force' => true]
            ], [
                'id' => 10,
                'ceasedDate' => $date,
                '_OPTIONS_' => ['force' => true]
            ],
            '_OPTIONS_' => [
                'multiple' => true
            ]
        ];

        $this->expectedRestCallInOrder('PsvDisc', 'PUT', $data);

        $saveData = [
            '_OPTIONS_' => [
                'multiple' => true
            ], [
                'licence' => 1,
                'ceasedDate' => null,
                'issuedDate' => null,
                'discNo' => null,
                'isCopy' => 'N'
            ], [
                'licence' => 1,
                'ceasedDate' => null,
                'issuedDate' => null,
                'discNo' => null,
                'isCopy' => 'N'
            ]
        ];

        $this->expectedRestCallInOrder('PsvDisc', 'POST', $saveData);

        $this->sm->setService('Helper\Data', new \Common\Service\Helper\DataHelperService());

        $this->sut->updateExistingForLicence(1);
    }
}
