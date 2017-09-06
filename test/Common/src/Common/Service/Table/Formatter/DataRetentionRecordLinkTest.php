<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DataRetentionRecordLink;
use Mockery as m;
use Zend\Mvc\Controller\Plugin\Url;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * DataRetentionRecord Link test
 */
class DataRetentionRecordLinkTest extends \PHPUnit_Framework_TestCase
{
    const ORGANISATION_NAME = 'DVSA';

    const ORGANISATION_ID = 'ORG123';

    const LIC_NO = 'OB1234';

    const LICENCE_ID = 9;

    const ENTITY_ID = 3;

    /**
     * @param array  $queryData           Query Data
     *
     * @dataProvider entityTypeDataProviderWithUrl
     */
    public function testFormat($queryData)
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

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                $this->getUrlHelperMock()
            )
            ->getMock();

        $this->assertEquals(
            '<a href="DATA_RETENTION_RECORD_URL" target="_self">' . $queryData['organisationName'] . '</a> / ' .
            '<a href="DATA_RETENTION_RECORD_URL" target="_self">' . $queryData['licNo'] . '</a> / ' .
            '<a href="DATA_RETENTION_RECORD_URL" target="_self">' .
            ucfirst($queryData['entityName']) . ' ' . $queryData['entityPk'] .
            '</a>',
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
                ['entityName' => 'licence', ],
            ],
            'Application entity type' => [
                ['entityName' => 'application', ],
            ],
            'Transport manager entity type' => [
                ['entityName' => 'transport_manager', ],
            ],
            'IRFO GV entity type' => [
                ['entityName' => 'irfo_gv_permit', ],
            ],
            'IRFO PSV auth entity type' => [
                ['entityName' => 'irfo_psv_auth', ],
            ],
            'Organisation entity type' => [
                ['entityName' => 'organisation', ],
            ],
            'Case entity type' => [
                ['entityName' => 'cases', ],
            ],
        ];
    }

    public function testWithoutLicenceNumberAndUndefinedEntity()
    {
        $sm = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->getMock();

        $queryData = [
            'entityName' => 'undefined',
            'organisationId' => self::ORGANISATION_ID,
            'organisationName' => 'DVSA',
            'entityPk' => self::ENTITY_ID,
            'licenceId' => self::LICENCE_ID,
            'licNo' => '123',
        ];

        $this->assertEquals(
            $queryData['organisationName'] . ' / ' .
            $queryData['licNo'] . ' / ' .
            $queryData['entityName'] . ' ' .
            $queryData['entityPk'],
            DataRetentionRecordLink::format($queryData, [], $sm)
        );
    }

    public function testWithoutLicenceNumberAndOrganisationAndUndefinedEntity()
    {
        $sm = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->getMock();

        $queryData = [
            'entityName' => 'undefined',
            'organisationName' => null,
            'organisationId' => self::ORGANISATION_ID,
            'entityPk' => self::ENTITY_ID,
            'licenceId' => self::LICENCE_ID,
            'licNo' => '123',
        ];

        $this->assertEquals(
            $queryData['licNo'] . ' / ' .
            $queryData['entityName'] . ' ' .
            $queryData['entityPk'],
            DataRetentionRecordLink::format($queryData, [], $sm)
        );
    }

    /**
     * @return mixed
     */
    private function getUrlHelperMock()
    {
        $urlHelper = m::mock(Url::class)
            ->shouldReceive('fromRoute')
            ->with(
                'licence',
                ['licence' => self::ENTITY_ID],
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
