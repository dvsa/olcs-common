<?php

namespace CommonTest\Filter\Publication;

use Common\Service\Data\Generic as GenericDataService;
use Common\Filter\Publication\ApplicationConditionUndertaking;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class ApplicationConditionUndertakingTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationConditionUndertakingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests the filter with no operating centre data
     *
     * @group publicationFilter
     * @dataProvider filterProvider
     *
     * @param $action
     * @param $expectedActionString
     */
    public function testFilterNoOc($action, $expectedActionString)
    {
        $applicationId = 8;
        $conditionTypeDescription = 'condition type description';
        $notes = 'notes';

        $inputData = [
            'application' => $applicationId
        ];

        $restResult = [
            0 => [
                'action' => $action,
                'conditionType' => [
                    'description' => $conditionTypeDescription
                ],
                'notes' => $notes,
                'operatingCentre' => []
            ]
        ];

        $expectedOutput = [
            'application' => $applicationId,
            'conditionUndertaking' => [
                0 => sprintf($expectedActionString, $conditionTypeDescription, $notes) . ' Attached to Licence.'
            ]
        ];

        $input = new Publication($inputData);
        $sut = new ApplicationConditionUndertaking();

        $params = [
            'application' => $applicationId,
            'limit' => 'all',
            'sort' => 'conditionType'
        ];

        $mockConditionUndertakingService = m::mock(GenericDataService::class);
        $mockConditionUndertakingService
            ->shouldReceive('fetchList')
            ->with($params)
            ->andReturn($restResult);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')
            ->with('Generic\Service\Data\ConditionUndertaking')
            ->andReturn($mockConditionUndertakingService);

        $sut->setServiceLocator($mockServiceManager);

        $this->assertEquals($expectedOutput, $sut->filter($input)->getArrayCopy());
    }

    /**
     * Provider for testFilter
     *
     * @return array
     */
    public function filterProvider()
    {
        $sut = new ApplicationConditionUndertaking();

        return [
            ['A', $sut::COND_NEW],
            ['D', $sut::COND_REMOVE],
            ['ZZZ', $sut::COND_NEW]
        ];
    }
}
