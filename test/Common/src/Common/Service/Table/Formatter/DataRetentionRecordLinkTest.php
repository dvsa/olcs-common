<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DataRetentionRecordLink;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Laminas\Mvc\Controller\Plugin\Url;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Common\View\Helper\Status as StatusHelper;

/**
 * DataRetentionRecord Link test
 */
class DataRetentionRecordLinkTest extends TestCase
{
    const ORGANISATION_NAME = 'DVSA';

    const ORGANISATION_ID = 'ORG123';

    const LIC_NO = 'OB1234';

    const LICENCE_ID = 9;

    const ENTITY_ID = 9;

    /**
     * @param array  $queryData           Query Data
     *
     * @dataProvider entityTypeDataProviderWithUrl
     */
    public function testFormat($queryData, $statusArray)
    {
        $queryData = array_merge(
            [
                'organisationName' => self::ORGANISATION_NAME,
                'organisationId' => self::ORGANISATION_ID,
                'licNo' => self::LIC_NO,
                'licenceId' => self::LICENCE_ID,
                'entityPk' => self::ENTITY_ID,
            ],
            $queryData
        );

        $statusLabel = 'status label';

        $sm = m::mock(ServiceLocatorInterface::class);

        $sm->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                $this->getUrlHelperMock()
            )
            ->getMock();

        $viewHelperManager = $this->getViewHelperWithStatusMock($statusArray, $statusLabel);
        $sm->shouldReceive('get')->with('ViewHelperManager')->once()->andReturn($viewHelperManager);

        $this->assertEquals(
            '<a href="DATA_RETENTION_RECORD_URL" target="_self">' . $queryData['organisationName'] . '</a> / ' .
            '<a href="DATA_RETENTION_RECORD_URL" target="_self">' . $queryData['licNo'] . '</a> / ' .
            '<a href="DATA_RETENTION_RECORD_URL" target="_self">' .
            ucfirst($queryData['entityName']) . ' ' . $queryData['entityPk'] .
            '</a>' .
            $statusLabel,
            DataRetentionRecordLink::format($queryData, [], $sm)
        );
    }

    /**
     * Parameter 1: query data
     * Parameter 2: URL parameters for last entity
     *
     * @return array
     */
    public function entityTypeDataProviderWithUrl()
    {
        return [
            'Licence entity type' => [
                [
                    'entityName' => 'licence',
                    'actionConfirmation' => false,
                    'nextReviewDate' => '2030-12-25'
                ],
                DataRetentionRecordLink::STATUS_POSTPONED
            ],
            'Application entity type' => [
                [
                    'entityName' => 'application',
                    'actionConfirmation' => true,
                    'nextReviewDate' => '2030-12-25'
                ],
                DataRetentionRecordLink::STATUS_DELETION
            ],
            'Transport manager entity type' => [
                [
                    'entityName' => 'transport_manager',
                    'actionConfirmation' => false,
                    'nextReviewDate' => null
                ],
                DataRetentionRecordLink::STATUS_REVIEW
            ],
            'IRFO GV entity type' => [
                [
                    'entityName' => 'irfo_gv_permit',
                    'actionConfirmation' => false,
                    'nextReviewDate' => '2030-12-25'
                ],
                DataRetentionRecordLink::STATUS_POSTPONED
            ],
            'IRFO PSV auth entity type' => [
                [
                    'entityName' => 'irfo_psv_auth',
                    'actionConfirmation' => true,
                    'nextReviewDate' => '2030-12-25'
                ],
                DataRetentionRecordLink::STATUS_DELETION
            ],
            'Organisation entity type' => [
                [
                    'entityName' => 'organisation',
                    'actionConfirmation' => false,
                    'nextReviewDate' => null
                ],
                DataRetentionRecordLink::STATUS_REVIEW
            ],
            'Case entity type' => [
                [
                    'entityName' => 'cases',
                    'actionConfirmation' => false,
                    'nextReviewDate' => '2030-12-25'
                ],
                DataRetentionRecordLink::STATUS_POSTPONED
            ],
            'Licence entity type to review' => [
                [
                    'entityName' => 'licence',
                    'actionConfirmation' => false,
                    'nextReviewDate' => (new \DateTime())->format('Y-m-d')
                ],
                DataRetentionRecordLink::STATUS_REVIEW
            ],
        ];
    }

    public function testWithoutLicenceNumberAndUndefinedEntity()
    {
        $sm = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->getMock();

        $statusLabel = 'statusLabel';

        $viewHelperManager = $this->getViewHelperWithStatusMock(DataRetentionRecordLink::STATUS_REVIEW, $statusLabel);
        $sm->shouldReceive('get')->with('ViewHelperManager')->once()->andReturn($viewHelperManager);

        $queryData = [
            'entityName' => 'undefined',
            'organisationId' => self::ORGANISATION_ID,
            'organisationName' => 'DVSA',
            'entityPk' => self::ENTITY_ID,
            'licenceId' => self::LICENCE_ID,
            'licNo' => '123',
            'actionConfirmation' => false,
            'nextReviewDate' => null
        ];

        $this->assertEquals(
            $queryData['organisationName'] . ' / ' .
            $queryData['licNo'] . ' / ' .
            $queryData['entityName'] . ' ' .
            $queryData['entityPk'] .
            $statusLabel,
            DataRetentionRecordLink::format($queryData, [], $sm)
        );
    }

    public function testWithoutLicenceNumberAndOrganisationAndUndefinedEntity()
    {
        $sm = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->getMock();

        $statusLabel = 'statusLabel';

        $viewHelperManager = $this->getViewHelperWithStatusMock(DataRetentionRecordLink::STATUS_REVIEW, $statusLabel);
        $sm->shouldReceive('get')->with('ViewHelperManager')->once()->andReturn($viewHelperManager);

        $queryData = [
            'entityName' => 'undefined',
            'organisationName' => null,
            'organisationId' => self::ORGANISATION_ID,
            'entityPk' => self::ENTITY_ID,
            'licenceId' => self::LICENCE_ID,
            'licNo' => '123',
            'actionConfirmation' => false,
            'nextReviewDate' => null
        ];

        $this->assertEquals(
            $queryData['licNo'] . ' / ' .
            $queryData['entityName'] . ' ' .
            $queryData['entityPk'] .
            $statusLabel,
            DataRetentionRecordLink::format($queryData, [], $sm)
        );
    }

    private function getViewHelperWithStatusMock($statusArray, $statusLabel)
    {
        $mockStatusHelper = m::mock(StatusHelper::class);
        $mockStatusHelper->shouldReceive('__invoke')
            ->once()
            ->with($statusArray)
            ->andReturn($statusLabel);

        $mockViewHelper = m::mock();
        $mockViewHelper->shouldReceive('get')->with('status')->andReturn($mockStatusHelper);

        return $mockViewHelper;
    }

    /**
     * @return mixed
     */
    private function getUrlHelperMock()
    {
        $urlHelper = m::mock(Url::class)
            ->shouldReceive('fromRoute')
            ->with(
                'licence-no',
                ['licNo' => self::LIC_NO],
                [],
                true
            )
            ->andReturn('DATA_RETENTION_RECORD_URL')
            ->shouldReceive('fromRoute')
            ->with(
                'licence',
                ['licence' => self::LICENCE_ID],
                [],
                true
            )
            ->andReturn('DATA_RETENTION_RECORD_URL')
            ->shouldReceive('fromRoute')
            ->with(
                'lva-application',
                ['application' => self::ENTITY_ID],
                [],
                true
            )
            ->andReturn('DATA_RETENTION_RECORD_URL')
            ->shouldReceive('fromRoute')
            ->with(
                'transport-manager',
                ['transportManager' => self::ENTITY_ID],
                [],
                true
            )
            ->andReturn('DATA_RETENTION_RECORD_URL')
            ->shouldReceive('fromRoute')
            ->with(
                'operator/business-details',
                ['organisation' => self::ORGANISATION_ID],
                [],
                true
            )
            ->andReturn('DATA_RETENTION_RECORD_URL')
            ->shouldReceive('fromRoute')
            ->with(
                'operator/irfo/gv-permits',
                [
                    'organisation' => self::ORGANISATION_ID,
                    'action' => 'details',
                    'id' => self::ENTITY_ID,
                ],
                [],
                true
            )
            ->andReturn('DATA_RETENTION_RECORD_URL')
            ->shouldReceive('fromRoute')
            ->with(
                'operator/irfo/psv-authorisations',
                [
                    'organisation' => self::ORGANISATION_ID,
                    'action' => 'edit',
                    'id' => self::ENTITY_ID,
                ],
                [],
                true
            )
            ->andReturn('DATA_RETENTION_RECORD_URL')
            ->shouldReceive('fromRoute')
            ->with(
                'case',
                [
                    'action' => 'details',
                    'case' => self::ENTITY_ID,
                ],
                [],
                true
            )
            ->andReturn('DATA_RETENTION_RECORD_URL')
            ->getMock();

        return $urlHelper;
    }
}
