<?php

/**
 * Community lic Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\CommunityLicEntityService;
use Mockery as m;

/**
 * Community lic Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicEntityServiceTest extends AbstractEntityServiceTestCase
{
    /**
     * @var string
     */
    protected $validStatuses = 'IN ["cl_sts_pending","cl_sts_active","cl_sts_withdrawn","cl_sts_suspended"]';

    protected function setUp()
    {
        $this->sut = new CommunityLicEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetList()
    {
        $query = [
            'foo' => 'bar'
        ];

        $this->expectOneRestCall('CommunityLic', 'GET', $query, ['children' => ['status']])
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getList($query));
    }

    /**
     * Test get office copy
     *
     * @group communityLicService
     * @dataProvider dataProvider
     */
    public function testGetOfficeCopy($results, $officeCopy)
    {
        $licenceId = 1;

        $query = [
            'issueNo' => 0,
            'status' => $this->validStatuses,
            'licence' => $licenceId
        ];

        $this->expectOneRestCall('CommunityLic', 'GET', $query)
            ->will($this->returnValue(['Results' => $results]));

        $this->assertEquals($this->sut->getOfficeCopy($licenceId), $officeCopy);
    }

    /**
     * Data provider for office copy
     *
     */
    public function dataProvider()
    {
        return [
            [['RESPONSE'], 'RESPONSE'],
            [[], null]
        ];
    }

    /**
     * Test get valid licences
     *
     * @group communityLicService
     */
    public function testGetValidLicences()
    {
        $licenceId = 1;
        $query = [
            'issueNo' => '!= 0',
            'status' => $this->validStatuses,
            'licence' => $licenceId,
            'sort'  => 'issueNo',
            'order' => 'ASC'
        ];
        $this->expectOneRestCall('CommunityLic', 'GET', $query);

        $this->sut->getValidLicences($licenceId);
    }

    /**
     * Test add office copy
     *
     * @group communityLicService
     */
    public function testAddOfficeCopy()
    {
        $licenceId = 1;

        $mockLicenceService = m::mock()
            ->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn(['id' => 'N'])
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $additionalData = [
            'licence' => $licenceId,
            'serialNoPrefix' => 'UKNI',
            'issueNo' => 0
        ];
        $data = [
            'somedata' => 'somedata'
        ];

        $this->expectOneRestCall('CommunityLic', 'POST', array_merge($data, $additionalData));

        $this->sut->addOfficeCopy($data, $licenceId);
    }

    /**
     * Test add community licences
     *
     * @group communityLicService
     */
    public function testAddCommuniyLicences()
    {
        $licenceId = 1;

        $queryForValidLicences = [
            'issueNo' => '!= 0',
            'status' => $this->validStatuses,
            'licence' => $licenceId,
            'sort'  => 'issueNo',
            'order' => 'ASC'
        ];
        $validLicences = [
            'Results' => [
                ['issueNo' => 1],
                ['issueNo' => 2]
            ],
            'Count' => 2
        ];
        $data = ['somedata' => 'somedata'];

        $dataToSave = [
            '_OPTIONS_' => ['multiple' => true],
            [
                'somedata' => 'somedata',
                'issueNo' => 3,
                'licence' => $licenceId,
                'serialNoPrefix' => 'UKNI'
            ],
            [
                'somedata' => 'somedata',
                'issueNo' => 4,
                'licence' => $licenceId,
                'serialNoPrefix' => 'UKNI'
            ]
        ];
        $this->expectedRestCallInOrder('CommunityLic', 'GET', $queryForValidLicences)
            ->will($this->returnValue($validLicences));

        $mockLicenceService = m::mock()
            ->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn(['id' => 'N'])
            ->getMock();

        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $this->expectedRestCallInOrder('CommunityLic', 'POST', $dataToSave);

        $this->sut->addCommunityLicences($data, $licenceId, 2);
    }

    /**
     * Test add community licences with specific issue numbers
     *
     * @group communityLicService
     */
    public function testAddCommuniyLicencesWithIssueNos()
    {
        $licenceId = 1;

        $data = ['somedata' => 'somedata'];

        $dataToSave = [
            '_OPTIONS_' => ['multiple' => true],
            [
                'somedata' => 'somedata',
                'issueNo' => 5,
                'licence' => $licenceId,
                'serialNoPrefix' => 'UKGB'
            ],
            [
                'somedata' => 'somedata',
                'issueNo' => 6,
                'licence' => $licenceId,
                'serialNoPrefix' => 'UKGB'
            ]
        ];

        $mockLicenceService = m::mock()
            ->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn(['id' => 'A'])
            ->getMock();

        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $this->expectOneRestCall('CommunityLic', 'POST', $dataToSave);

        $this->sut->addCommunityLicencesWithIssueNos($data, $licenceId, [5, 6]);
    }

    /**
     * @group communityLicService
     */
    public function testGetWithLicence()
    {
        $query = [
            'foo' => 'bar'
        ];

        $bundle = [
            'children' => [
                'status',
                'licence' => [
                    'children' => [
                        'licenceType'
                    ]
                ]
            ]
        ];

        $this->expectOneRestCall('CommunityLic', 'GET', $query, $bundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getWithLicence($query));
    }

    /**
     * @group communityLicService
     */
    public function testGetPendingForLicence()
    {
        $licenceId = 123;

        $query = [
            'licence' => $licenceId,
            'specifiedDate' => 'NULL',
            'status' => 'cl_sts_pending',
            'limit' => 'all'
        ];

        $this->expectOneRestCall('CommunityLic', 'GET', $query)
            ->will($this->returnValue(['Results' => 'RESPONSE']));

        $this->assertEquals('RESPONSE', $this->sut->getPendingForLicence($licenceId));
    }

    /**
     * @group communityLicService
     */
    public function testGetActiveLicences()
    {
        $licenceId = 1;
        $query = [
            'status' => 'cl_sts_active',
            'licence' => $licenceId,
            'sort'  => 'issueNo',
            'order' => 'ASC'
        ];
        $this->expectOneRestCall('CommunityLic', 'GET', $query);

        $this->sut->getActiveLicences($licenceId);
    }

    /**
     * @group communityLicService
     * @todo
     */
    public function testGetByIds()
    {
        $this->markTestIncomplete("TODO");
    }
}
